<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
// use GuzzleRetry\GuzzleRetryMiddleware;
use Libs\OAuth2\InvalidGrantTypeException;
use Libs\OAuth2\OAuth2InvalidIntrospectionResponse;
use libs\oauth2\OAuth2Protocol;

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
{    private static $api_prefix = 'oidc/session/bootstrap';

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
            // Validate request method and headers
            $response = $this->validateRequestData($request);
            if ($response instanceof SS_HTTPResponse) {
                return $response; // Return the error response
            }
            $data = &$response['data'];
            $accessToken = &$response['accessToken'];

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
            $externalResponse = $this->doIntrospectionRequest($accessToken);

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
     * Validate request data
     * @param array $data
     */
    private function validateRequestData(SS_HTTPRequest $request): array|SS_HTTPResponse
    {
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

        // Check X-CSRF-Token header value
        if ($csrfToken !== $this->getSecurityToken()) {
            return $this->badRequest('X-CSRF-Token header is invalid');
        }

        // Get JSON payload
        $data = $this->getJsonRequest();
        if (!$data) {
            return $this->badRequest('Invalid JSON payload');
        }

        return [
            'data' => $data,
            'accessToken' => $accessToken
        ];
    }

    /**
     * Make a dummy call to an external server
     * @param string $token_value
     * @param array $data
     * @return array
     */
    private function doIntrospectionRequest($token_value): array|bool
    {
        try {
            SS_Log::log(sprintf(__METHOD__ . " token %s", $token_value), SS_Log::DEBUG);
            $stack = HandlerStack::create();
            // $stack->push(GuzzleRetryMiddleware::factory());
            $client = new Client([
                'handler'         => $stack,
                'timeout'         => ini_get('curl.timeout') ?: 60,
                'allow_redirects' => ini_get('curl.allow_redirects') ?: false,
                'verify'          => ini_get('curl.verify_ssl_cert') ?: true,

            ]);

            $client_id       = defined('OIDC_CLIENT_ID_PUBLIC') ? OIDC_CLIENT_ID_PUBLIC : '';
            $client_secret   = defined('OIDC_CLIENT_SECRET_PUBLIC') ? OIDC_CLIENT_SECRET_PUBLIC : '';
            $auth_server_url = defined('IDP_OPENSTACKID_URL') ? IDP_OPENSTACKID_URL : '';

            if (empty($client_id)) {
                return [
                    'status' => 'error',
                    'http_code' => 500,
                    'message' => 'OIDC_CLIENT_ID_PUBLIC is not configured'
                ];
            }

            if (empty($client_secret)) {
                return [
                    'status' => 'error',
                    'http_code' => 500,
                    'message' => 'OIDC_CLIENT_SECRET_PUBLIC is not configured'
                ];
            }

            if (empty($auth_server_url)) {
                return [
                    'status' => 'error',
                    'http_code' => 500,
                    'message' => 'IDP_OPENSTACKID_URL is not configured'
                ];
            }

            // http://docs.guzzlephp.org/en/stable/request-options.html
            $response = $client->request('POST',
                  "{$auth_server_url}/oauth2/token/introspection",
                [
                    'form_params'  => ['token' => $token_value],
                    'auth'         => [$client_id, $client_secret],
                    'timeout'      => 120,
                    'http_errors' => true
                ]
            );

            $content_type = $response->getHeaderLine('content-type');
            if($content_type !== 'application/json')
            {
                // invalid content type
                $body = $response->getBody()->getContents();
                $status = $response->getStatusCode();
                SS_Log::log(sprintf(__METHOD__ . " status %s content type %s body %s", $status, $content_type, $body), SS_Log::WARN);
                throw new \Exception($body);
            }
            return json_decode($response->getBody()->getContents(), true);

        }
        catch (RequestException $ex) {
            $this->handleInstropectionException($ex, $token_value);
            return false;
        }
    }

    protected function handleInstropectionException(RequestException &$ex, string $token_value)
    {
        SS_Log::log($ex, SS_Log::WARN);
        $response  = $ex->getResponse();

        if(is_null($response))
            throw new Exception(sprintf('http code %s', $ex->getCode()));
        $content_type = $response->getHeaderLine('content-type');
        $is_json      = $content_type === 'application/json';
        $body         = $response->getBody()->getContents();
        $code         = $response->getStatusCode();

        SS_Log::log(sprintf("%s token %s code %s body %s", __METHOD__, $token_value, $code, $body), SS_Log::WARN);

        $invalid = [
            OAuth2Protocol::OAuth2Protocol_Error_InvalidToken,
            OAuth2Protocol::OAuth2Protocol_Error_InvalidGrant
        ];

        if ($code === 400 && $is_json && isset($body['error']) && (in_array($body['error'], $invalid)))
        {
            SS_Log::log(sprintf("%s token %s marked as revoked (400 %s)", __METHOD__, $token_value, $body['error']), SS_Log::WARN);
            throw new InvalidGrantTypeException($body['error']);
        }

        if ($code == 503 )
        {
            // service went offline temporally ... revoke token
            SS_Log::log(sprintf("%s token %s marked as revoked (503 offline IDP)", __METHOD__, $token_value), SS_Log::WARN);
            throw new InvalidGrantTypeException(OAuth2Protocol::OAuth2Protocol_Error_InvalidToken);
        }
        SS_Log::log(sprintf("%s token %s OAuth2InvalidIntrospectionResponse (%s %s)", __METHOD__, $token_value, $ex->getCode(), $body), SS_Log::WARN);
        throw new OAuth2InvalidIntrospectionResponse(sprintf('http code %s - body %s', $ex->getCode(), $body));
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
