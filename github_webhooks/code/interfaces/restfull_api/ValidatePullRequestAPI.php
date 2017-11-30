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

/**
 * Class ValidatePullRequestAPI
 */
class ValidatePullRequestAPI extends AbstractRestfulJsonApi  {


    private static $api_prefix = 'api/v1/pull-requests';

	/**
	 * @var IPullRequestManager
	 */
	private $manager;

    /**
     * ValidatePullRequestAPI constructor.
     * @param IPullRequestManager $manager
     */
	public function __construct(IPullRequestManager $manager)
	{
		parent::__construct();
		$this->manager = $manager;
	}

	protected function authenticate() {
		return true;
	}

	private static $allowed_github_events = ['pull_request', 'ping'];
	/**
	 * @return bool
	 */
	protected function authorize()
	{
		$request                  = $this->request;
		$git_hub_event_header     = $request->getHeader('X-Github-Event');
		return in_array($git_hub_event_header, self::$allowed_github_events);
	}

    protected function isPullRequestEvent(){
        $request                  = $this->request;
        $git_hub_event_header     = $request->getHeader('X-Github-Event');
        return ($git_hub_event_header == 'pull_request');
    }

	/**
	 * @var array
	 */
	static $url_handlers = [
		'POST ' => 'validatePullRequest',
	];

	/**
	 * @var array
	 */
	static $allowed_actions = [
		'validatePullRequest',
	];

	public function validatePullRequest(){

		try{
		    $request                  = $this->request;
            $git_hub_signature_header = $request->getHeader('X-Hub-Signature');

			if( $this->isPullRequestEvent()){
                $this->manager->registerPullRequest($request->getBody(), ['X-Hub-Signature' => $git_hub_signature_header]);
			}
			return $this->ok();
		}
		catch(NotFoundEntityException $ex1){
			SS_Log::log($ex1,SS_Log::NOTICE);
			return $this->notFound($ex1->getMessage());
		}
        catch(SecurityException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->permissionFailure();
        }
        catch(UnAuthorizedUser $ex3){
            SS_Log::log($ex3,SS_Log::WARN);
            return $this->permissionFailure();
        }
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			return $this->serverError();
		}
	}
}