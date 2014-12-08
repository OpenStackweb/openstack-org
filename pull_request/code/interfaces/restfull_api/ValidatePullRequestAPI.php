<?php
/**
 * Copyright 2014 Openstack Foundation
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

class ValidatePullRequestAPI extends AbstractRestfulJsonApi  {


	const ApiPrefix = 'api/v1/pulls';

	/**
	 * @var GithubPullRequestManager
	 */
	private $manager;

	public function __construct()
	{
		parent::__construct();
		$this->manager = new GithubPullRequestManager();
	}

	protected function isApiCall()
	{
		$request = $this->getRequest();
		if(is_null($request)) return false;
		return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
	}

	protected function authenticate() {
		return true;
	}

	/**
	 * @return bool
	 */
	protected function authorize()
	{
		$data                     = $this->getJsonRequest();
		$request                  = $this->request;
		$git_hub_event_header     = $request->getHeader('X-Github-Event');
		$git_hub_signature_header = $request->getHeader('X-Hub-Signature');
		$res = false;
		if($git_hub_event_header == 'pull_request')
			$res = true;
		return $res;
	}

	/**
	 * @var array
	 */
	static $url_handlers = array(
		'POST ' => 'validatePullRequest',
	);

	/**
	 * @var array
	 */
	static $allowed_actions = array(
		'validatePullRequest',
	);

	public function validatePullRequest(){
		$data = $this->getJsonRequest();
		try{

			if( $data!=null &&
				isset($data['action']) &&
				($data['action']=='opened' || $data['action']=='reopened') &&
				isset($data['pull_request']) &&
				$pull_request = $data['pull_request'])
			{
				$this->manager->validatePullRequest($pull_request);

			}
		}
		catch(NotFoundEntityException $ex1){
			SS_Log::log($ex1,SS_Log::NOTICE);
			return $this->notFound($ex1->getMessage());
		}
		catch(EntityValidationException $ex2){
			SS_Log::log($ex2,SS_Log::NOTICE);
			return $this->validationError($ex2->getMessages());
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			return $this->serverError();
		}
		if (!$data) return $this->serverError();
	}
}