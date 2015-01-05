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
 * Class JobCrudApi
 */
final class JobCrudApi
extends AbstractRestfulJsonApi {

	const ApiPrefix = 'api/v1/jobs';

	protected function isApiCall(){
		$request = $this->getRequest();
		if(is_null($request)) return false;
		return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
	}

	protected function authenticate() {
		//we dont need authentication
		return true;
	}

	/**
	 * @return bool
	 */
	protected function authorize(){
		return true;
	}

	/**
	 * @var JobManager
	 */
	private $manager;

	/**
	 * @var IJobRepository
	 */
	private $repository;

	/**
	 * @var IQueryHandler
	 */
	private $companies_names_query;

	/// FILTERS
	/**
	 * @param $request
	 * @return SS_HTTPResponse
	 */
	public function checkJobTasksAuthentication($request){
		$auth_response = $this->isHttpBasicAuthPresent();
		if(!$auth_response)
			return $this->unauthorizedHttpBasicAuth('Restricted area');

		list($user,$password) = $auth_response;
		if($user != JOB_TASKS_USER || $password != JOB_TASKS_PASS){
			return $this->unauthorizedHttpBasicAuth('Restricted area');
		}
	}

	/**
	 * @param $request
	 * @return SS_HTTPResponse
	 */
	public function checkSangriaAccess($request){
		if(!Permission::check("SANGRIA_ACCESS"))
			return $this->permissionFailure();
	}

	public function __construct(){
		parent::__construct();
		$this->companies_names_query = new CompaniesNamesQueryHandler;
		$this->repository            = new SapphireJobRepository;
		$this->manager               = new JobManager(
			$this->repository,
			new SapphireJobAlertEmailRepository,
			new JobFactory,
			new JobsValidationFactory,
			new SapphireJobPublishingService,
			SapphireTransactionManager::getInstance()
		);

		//filters
		$this_var = $this;
		$this->addBeforeFilter('updateJob','check_access_reject',function ($request) use($this_var){
			return $this_var->checkSangriaAccess($request);
		});
		$this->addBeforeFilter('getJob','check_access_update',function ($request) use($this_var){
			return $this_var->checkSangriaAccess($request);
		});
        $this->addBeforeFilter('toggleFoundationJob','check_access_update',function ($request) use($this_var){
            return $this_var->checkSangriaAccess($request);
        });
        $this->addBeforeFilter('deleteJob','check_access_reject',function ($request) use($this_var){
            return $this_var->checkSangriaAccess($request);
        });
	}

	/**
	 * @var array
	 */
	static $url_handlers = array(
		'GET companies'                 => 'companies',
        'PUT $JOB_ID/toggle_foundation' => 'toggleFoundationJob',
		'PUT $JOB_ID/delete'            => 'deleteJob',
		'GET $JOB_ID'                   => 'getJob',
        'PUT '                          => 'updateJob',
        'POST '                         => 'addJob',
	);

	/**
	 * @var array
	 */
	static $allowed_actions = array(
		'deleteJob',
		'getJob',
		'updateJob',
		'companies',
        'toggleFoundationJob',
        'addJob',
	);


    /**
     * @return SS_HTTPResponse
     */
    public function toggleFoundationJob(){
        try{
            $job_id = (int)$this->request->param('JOB_ID');
            $this->manager->toggleFoundationJob($job_id);
            return $this->updated();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::ERR);
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
    }

    /**
     * @return SS_HTTPResponse
     */
    public function getJob(){
        $job_id = (int)$this->request->param('JOB_ID');
        try{
            $job = $this->repository->getById($job_id);
            if(!$job) throw new NotFoundEntityException('Job',sprintf('id %s',$job_id));
            return $this->ok(JobsAssembler::convertJobToArray($job));
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::ERR);
            return $this->notFound($ex1->getMessage());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @return SS_HTTPResponse
     */
    public function updateJob(){
        try{
            $data = $this->getJsonRequest();
            if (!$data) return $this->serverError();
            $this->manager->updateJob($data);
            return $this->updated();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::ERR);
            return $this->notFound($ex1->getMessage());
        }
        catch (EntityValidationException $ex2) {
            SS_Log::log($ex2,SS_Log::ERR);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @return SS_HTTPResponse
     */
    public function deleteJob(){
        try{
            $job_id = (int)$this->request->param('JOB_ID');
            $this->manager->deleteJob($job_id);
            return $this->updated();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::ERR);
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
    }

    public function addJob(){
        try{
            $data = $this->getJsonRequest();
            if (!$data) return $this->serverError();
            $job_id = $this->manager->addJob($data);
            return $job_id;
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch (EntityValidationException $ex2) {
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }
}