<?php

require_once __DIR__ . '/Libs/OAuth2/InvalidGrantTypeException.php';
require_once __DIR__ . '/Libs/OAuth2/OAuth2InvalidIntrospectionResponse.php';
require_once __DIR__ . '/Libs/OAuth2/OAuth2Protocol.php';


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use Libs\OAuth2\InvalidGrantTypeException;
use Libs\OAuth2\OAuth2InvalidIntrospectionResponse;
use Libs\OAuth2\OAuth2Protocol;

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
{
	private static $api_prefix = 'oidc/session/bootstrap';

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
		if (is_null($request))
			return false;
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
			$response = $this->validateRequestData($request);
			if ($response instanceof SS_HTTPResponse) {
				return $response;
			}
			$data = &$response['data'];
			$accessToken = &$response['accessToken'];

			try {
				$tokenData = $this->doIntrospectionRequest($accessToken);

				if ($tokenData instanceof SS_HTTPResponse) {
					return $tokenData;
				}

				if ($tokenData === false)
				{
					SS_Log::log(sprintf("%s token %s introspection returned false", __METHOD__, $accessToken), SS_Log::WARN);
				}

				if (
						$tokenData === false or
						(is_array($tokenData) and isset($tokenData['error']))
					) {
					$error = 'Invalid or expired access token';
					if (is_array($tokenData) and !empty($tokenData['error'])) {
						$error = $tokenData['error'];
					}
					return $this->validationError([$error], 400);
				}

				// at this point we have a valid and active token
				// we can create a session for the user
				$member = null;

				if (!$member && $tokenData['user_email']) {
					$member = Member::get()->filter('Email', $tokenData['user_email'])->first();
				}

				if (!$member) {
					return $this->validationError(['User not found'], 404);
				}
				// log in the user
				$member->logIn();
				SS_Log::log(
					sprintf('OIDC session bootstrap successful for token: %s', substr($accessToken, 0, 10) . '...'),
					SS_Log::DEBUG
				);

				return $this->noContent();
			} catch (Exception $ex) {
				return $this->validationError([$ex->getMessage()], 400);
			}

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
	 * @return array|SS_HTTPResponse
	 */
	private function validateRequestData(SS_HTTPRequest $request)
	{
		// Check if request method is POST
		if (!$request->isPOST()) {
			return $this->methodNotAllowed();
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
	 * @return array|bool|SS_HTTPResponse
	 */
	private function doIntrospectionRequest(string $token_value)
	{
		try {
			SS_Log::log(sprintf(__METHOD__ . " token %s", $token_value), SS_Log::DEBUG);

			$client_id = defined('OIDC_PUBLIC_APP_CLIENT_ID') ? OIDC_PUBLIC_APP_CLIENT_ID : '';
			$client_secret = defined('OIDC_PUBLIC_APP_CLIENT_SECRET') ? OIDC_PUBLIC_APP_CLIENT_SECRET : '';
			$auth_server_url = defined('IDP_OPENSTACKID_URL') ? IDP_OPENSTACKID_URL : '';
			$verify = defined('OIDC_PUBLIC_APP_VERIFY_HOST') ? OIDC_PUBLIC_APP_VERIFY_HOST : false;

			if (empty($client_id)) {
				return $this->validationError('OIDC_CLIENT_ID_PUBLIC is not configured');
			}

			if (empty($client_secret)) {
				return $this->validationError('OIDC_CLIENT_SECRET_PUBLIC is not configured');
			}

			if (empty($auth_server_url)) {
				return $this->validationError('IDP_OPENSTACKID_URL is not configured');
			}

			$stack = HandlerStack::create();
			$stack->push(GuzzleRetryMiddleware::factory());

			$client = new Client([
				'base_uri' => $auth_server_url,
				'handler' => $stack,
				'verify' => $verify,
				'timeout' => 120,
			]);

			// http://docs.guzzlephp.org/en/stable/request-options.html
			$options = [
				'form_params' => ['token' => $token_value],
				'auth' => [$client_id, $client_secret],
				'http_errors' => true,
				'synchronous' => true,
			];
			$endpoint = "/oauth2/token/introspection";
			SS_Log::log(sprintf(__METHOD__ . " DEBUG options %s", json_encode($options)), SS_Log::DEBUG);
			$response = $client->request('POST', $endpoint, $options);

			$content_type = $response->getHeaderLine('content-type');
			$is_json = in_array("application/json", explode(';', $content_type));
			$body = $response->getBody()->getContents();
			if (!$is_json) {
				// invalid content type
				$status = $response->getStatusCode();
				SS_Log::log(sprintf(__METHOD__ . " status %s content type %s body %s", $status, $content_type, $body), SS_Log::WARN);
				return $this->validationError($body, 500);
			}
			SS_Log::log(sprintf(__METHOD__ . " DEBUG response %s", $body), SS_Log::DEBUG);

			$jsonResponse = json_decode($body, true);
			if (!$jsonResponse) {
				SS_Log::log(sprintf(__METHOD__ . " invalid JSON response %s", $body), SS_Log::ERR);
				return $this->validationError('Invalid JSON response from IDP', 500);
			}

			SS_Log::log(sprintf(__METHOD__ . " DEBUG JSON RESPONSE %s", json_encode($jsonResponse, JSON_PRETTY_PRINT)), SS_Log::DEBUG);
			$defaults = array_fill_keys(["error", "user_id", "user_external_id", "user_identifier", "user_email", "active"], null);
			$defaults['active'] = false;
			$jsonResponse = array_merge($defaults, $jsonResponse);

			$json = [
				'user_id' => $jsonResponse['user_id'] ?: $jsonResponse['user_external_id'],
				'user_email' => $jsonResponse['user_email'] ?: $jsonResponse['user_identifier'],
			];
			SS_Log::log(sprintf(__METHOD__ . " DEBUG JSON RETURN %s", json_encode($json, JSON_PRETTY_PRINT)), SS_Log::DEBUG);
			return $json;

		} catch (RequestException $ex) {
			$this->handleInstropectionException($ex, $token_value);
		} catch (Exception $ex) {
			SS_Log::log($ex, SS_Log::ERR);

			$data = [
				"error" => "Server Error",
				"message" => $ex->getMessage(),
			];
			if (defined('SS_ENVIRONMENT_TYPE') && SS_ENVIRONMENT_TYPE !== 'production') {
				$data['trace'] = $ex->getTrace();
			}
			$response = new SS_HTTPResponse();
			$response->setStatusCode(500);
			$response->addHeader('Content-Type', 'application/json');
			$response->setBody(json_encode($data));
			return $response;
		}

		return false;
	}

	protected function handleInstropectionException(RequestException &$ex, string $token_value)
	{
		SS_Log::log($ex, SS_Log::WARN);
		$response = $ex->getResponse();

		if (is_null($response))
			throw new Exception(sprintf('http code %s', $ex->getCode()));

		$content_type = $response->getHeaderLine('content-type');
		$is_json = in_array("application/json", explode(';', $content_type));
		$body = $response->getBody()->getContents();
		$code = $response->getStatusCode();

		if ($is_json) {
			$body = json_decode($body, true);
		}
		SS_Log::log("Error Response:", SS_Log::WARN, $body);

		$invalid = [
			OAuth2Protocol::OAuth2Protocol_Error_InvalidToken,
			OAuth2Protocol::OAuth2Protocol_Error_InvalidGrant
		];

		if ($code === 400 && $is_json && isset($body['error']) && in_array($body['error'], $invalid)) {
			SS_Log::log(sprintf("%s token %s invalid (400 %s)", __METHOD__, $token_value, $body['error']), SS_Log::WARN);
			throw new InvalidGrantTypeException($body['error']);
		}

		if ($code == 503) {
			SS_Log::log(sprintf("%s token %s invalid (503 offline IDP)", __METHOD__, $token_value), SS_Log::WARN);
			throw new InvalidGrantTypeException(OAuth2Protocol::OAuth2Protocol_Error_InvalidToken);
		}
		SS_Log::log(sprintf("%s token %s OAuth2InvalidIntrospectionResponse (%s %s)", __METHOD__, $token_value, $ex->getCode(), $body), SS_Log::WARN);
		throw new OAuth2InvalidIntrospectionResponse(sprintf('http code %s - body %s', $code, $body));
	}

	/**
	 * Return method not allowed response
	 * @param string $message
	 * @return SS_HTTPResponse
	 */
	protected function methodNotAllowed()
	{
		return parent::methodNotAllowed()
			->setBody(json_encode("Only POST requests are allowed"))
			->addHeader('Allow', 'POST');
	}

	protected function getSecurityToken()
	{
		return SecurityToken::inst() ? SecurityToken::inst()->getValue() : null;
	}
}