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
 * Class OIDCSessionWhoAmIApi
 * Handles OIDC session whoami requests
 */
final class OIDCSessionWhoAmIApi extends AbstractRestfulJsonApi
{
	private static $api_prefix = 'oidc/session/whoami';

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

			$member = Member::currentUser();
			if (!$member) {
				$response = $this->notFound('No authenticated user found');
			} else {
				$response = $this->ok([
					'user_id' => $member->ID,
					'email' => $member->Email,
				]);
			}
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
	 * @param SS_HTTPRequest $request
	 * @return bool|SS_HTTPResponse
	 */
	private function validateRequestData(SS_HTTPRequest $request)
	{
		// Check if request method is POST
		if (!$request->isGET()) {
			return $this->methodNotAllowed();
		}

		// Check X-CSRF-Token header
		$csrfToken = $request->getHeader('X-CSRF-Token') ?: $request->getHeader('X-Csrf-Token');
		if (empty($csrfToken)) {
			return $this->validationError(['X-CSRF-Token header is required', "headers" => $request->getHeaders()]);
		}

		// Check X-CSRF-Token header value
		if ($csrfToken !== Session::get('SecurityID')) {
			return $this->badRequest('X-CSRF-Token header is invalid');
		}

		return true;
	}

	/**
	 * Return method not allowed response
	 * @param string $message
	 * @return SS_HTTPResponse
	 */
	protected function methodNotAllowed()
	{
		return parent::methodNotAllowed()
			->setBody(json_encode("Only GET requests are allowed"))
			->addHeader('Allow', 'GET');
	}


	/**
	 * Return method bad request response
	 * @param string $error
	 * @return SS_HTTPResponse
	 */
	protected function badRequest(string $error)
	{
		$response = new SS_HTTPResponse();
		$response->setStatusCode(400);
		$response->addHeader('Content-Type', 'application/json');
		$response->setBody(json_encode(["error" => $error]));
		return $response;
	}

	protected function getSecurityToken()
	{
		return SecurityToken::inst() ? SecurityToken::inst()->getValue() : null;
	}
}