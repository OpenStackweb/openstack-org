<?php
/**
 * Copyright 2025 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class OIDCSessionBootstrapApi
 * Handles OIDC session bootstrap requests
 */
final class OIDCSessionBootstrapApi extends AbstractRestfulJsonApi
{    private static $api_prefix = 'api/v1/oidc/session/bootstrap';

    /**
     * @var array
     */
    private static $url_handlers = [
        'POST ' => 'index',
    ];

    /**
     * @var array
     */
    private static $allowed_actions = [
        'index',
    ];

    /**
     * @return bool
     */
    protected function isApiCall()
    {
        $request = $this->getRequest();
        if (is_null($request)) return false;
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function authenticate()
    {
        return true;
    }

    /**
     * Bootstrap OIDC session
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function index(SS_HTTPRequest $request)
    {
        try {
            // Check if request method is POST
            if (!$request->isPOST()) {
                return $this->methodNotAllowed('Only POST requests are allowed');
            }

            // Check Content-Type header
            $contentType = $request->getHeader('Content-Type');
            if (strpos($contentType, 'application/json') === false) {
                return $this->validationError(['Content-Type must be application/json']);
            }

            // Check Authorization header
            $authHeader = $request->getHeader('Authorization');
            if (empty($authHeader) || strpos($authHeader, 'Bearer ') !== 0) {
                return $this->validationError(['Authorization header with Bearer token is required']);
            }

            // Extract access token
            $accessToken = str_replace('Bearer ', '', $authHeader);
            if (empty($accessToken)) {
                return $this->validationError(['Access token is required']);
            }

            // Check X-CSRF-Token header
            $csrfToken = $request->getHeader('X-CSRF-Token') ?: $request->getHeader('X-Csrf-Token');
            if (empty($csrfToken)) {
                return $this->validationError(['X-CSRF-Token header is required', "headers" => $request->getHeaders()]);
            }

            // Check X-CSRF-Token header

            if ($csrfToken !== $this->getSecurityToken()) {
                return $this->badRequest('X-CSRF-Token header is invalid');
            }

            // Get JSON payload
            $data = $this->getJsonRequest();
            if (!$data) {
                return $this->badRequest('Invalid JSON payload');
            }

            // Validate access token with OIDC provider
            try {
                $oidc = OIDCClientFactory::build();
                $tokenData = $oidc->introspectToken($accessToken);

                if (isset($tokenData->error) || (isset($tokenData->active) && !$tokenData->active)) {
                    return $this->validationError(['Invalid or expired access token']);
                }
            } catch (Exception $ex) {
                SS_Log::log($ex, SS_Log::WARN);
                return $this->validationError(['Token validation failed']);
            }

            // Make dummy call to external server
            $externalResponse = $this->makeExternalCall($accessToken, $data);

            // Log the bootstrap attempt
            SS_Log::log(
                sprintf('OIDC session bootstrap successful for token: %s', substr($accessToken, 0, 10) . '...'),
                SS_Log::INFO
            );

			$success = $externalResponse['status'] === 'success';
			$response = new SS_HTTPResponse();
			$response->setStatusCode($success ? $externalResponse['http_code'] : 400);
			$response->addHeader('Content-Type', 'application/json');
			$response->setBody($success ? json_encode($externalResponse['data']) : json_encode(['error' => $externalResponse['message']]));
			// $response->setBody('');
            return $response;

        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * Make a dummy call to an external server
     * @param string $accessToken
     * @param array $data
     * @return array
     */
    private function makeExternalCall($accessToken, $data)
    {
        try {
            // Dummy external API endpoint (replace with actual external server URL)
            $externalUrl = 'https://jsonplaceholder.typicode.com/posts';

            // Prepare payload for external call
            $payload = [
                'title' => 'OIDC Session Bootstrap',
                'body' => 'Session bootstrap request from OpenStack.org',
                'userId' => 1,
                'metadata' => [
                    'timestamp' => isset($data['timestamp']) ? $data['timestamp'] : time(),
                    'userAgent' => isset($data['userAgent']) ? $data['userAgent'] : 'Unknown',
                    'token_hash' => hash('sha256', $accessToken) // Don't send the actual token
                ]
            ];

            // Initialize cURL
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $externalUrl,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'User-Agent: OpenStack.org/1.0'
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                SS_Log::log("External API call failed: " . $error, SS_Log::WARN);
                return [
                    'status' => 'error',
                    'message' => 'External API call failed',
                    'error' => $error
                ];
            }

            if ($httpCode >= 200 && $httpCode < 300) {
                $responseData = json_decode($response, true);
                return [
                    'status' => 'success',
                    'http_code' => $httpCode,
                    'data' => $responseData ?: ['raw_response' => $response]
                ];
            } else {
                return [
                    'status' => 'error',
                    'http_code' => $httpCode,
                    'message' => 'External API returned error status'
                ];
            }

        } catch (Exception $ex) {
            SS_Log::log("External API call exception: " . $ex->getMessage(), SS_Log::ERR);
            return [
                'status' => 'error',
                'message' => 'External API call failed with exception',
                'error' => $ex->getMessage()
            ];
        }
    }

    /**
     * Return method not allowed response
     * @param string $message
     * @return SS_HTTPResponse
     */
    protected function methodNotAllowed($message = 'Method Not Allowed')
    {
        return (new SS_HTTPResponse($message, 405))
            ->addHeader('Content-Type', 'application/json')
            ->addHeader('Allow', 'POST');
    }

    protected function getSecurityToken()
    {
        return SecurityToken::inst() ? SecurityToken::inst()->getValue() : null;
    }
}
