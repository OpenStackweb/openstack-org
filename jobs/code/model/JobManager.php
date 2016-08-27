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
 * Class JobManager
 */
final class JobManager implements IJobManager {
	/**
	 * @var IJobsValidationFactory
	 */
	private $validator_factory;
	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;
	/**
	 * @var IJobFactory
	 */
	private $factory;

	/**
	 * @var IJobPublishingService
	 */
	private $jobs_publishing_service;

	/**
	 * @var IEntityRepository
	 */
	private $jobs_repository;

	/**
	 * @var IAlertEmailRepository
	 */
	private $email_repository;

	/**
	 * @param IEntityRepository      $jobs_repository
	 * @param IAlertEmailRepository  $email_repository
	 * @param IJobFactory            $factory
	 * @param IJobsValidationFactory $validator_factory
	 * @param IJobPublishingService  $jobs_publishing_service
	 * @param ITransactionManager    $tx_manager
	 */
	public function __construct(IEntityRepository $jobs_repository,
	                            IAlertEmailRepository $email_repository,
	                            IJobFactory $factory,
	                            IJobsValidationFactory $validator_factory,
	                            IJobPublishingService $jobs_publishing_service,
	                            ITransactionManager $tx_manager){

		$this->jobs_repository         = $jobs_repository;
		$this->email_repository        = $email_repository;
		$this->validator_factory       = $validator_factory;
		$this->factory                 = $factory;
		$this->jobs_publishing_service = $jobs_publishing_service;
		$this->tx_manager              = $tx_manager;
	}

    /**
     * @param $id
     * @return IJob
     */
    public function toggleFoundationJob($id){
        $jobs_repository          = $this->jobs_repository;

        $job =  $this->tx_manager->transaction(function() use ($id, $jobs_repository){
            $job = $jobs_repository->getById($id);
            if(!$job) throw new NotFoundEntityException('Job',sprintf('id %s',$id ));
            $job->toggleFoundation();

            return $job;
        });


        return $job;
    }

    /**
     * @param array $data
     * @return IJobRegistrationRequest
     */
    public function updateJob(array $data){
        $validator_factory = $this->validator_factory;
        $repository        = $this->jobs_repository ;
        $factory           = $this->factory;
        return $this->tx_manager->transaction(function() use($data, $repository, $validator_factory, $factory){
            $validator = $validator_factory->buildValidatorForJob($data);
            if ($validator->fails()) {
                throw new EntityValidationException($validator->messages());
            }

            $job = $repository->getById(intval($data['id']));
            if(!$job)
                throw new NotFoundEntityException('Job',sprintf('id %s',$data['id'] ));

            $job->registerMainInfo($factory->buildJobMainInfo($data));
            $locations = $factory->buildJobLocations($data);
            $job->clearLocations();
            foreach($locations as $location)
                $job->addLocation($location);

            return $job;
        });
    }

    /**
     * @param $id
     * @return void
     */
    public function deleteJob($id){
        $jobs_repository = $this->jobs_repository;

        $this->tx_manager->transaction(function() use ($id, $jobs_repository){
            $job = $jobs_repository->getById($id);
            $jobs_repository->delete($job);
        });
    }

    /**
     * @param array $data
     * @return IJob
     */
    public function addJob(array $data){

        $validator_factory  = $this->validator_factory;
        $repository         = $this->jobs_repository;
        $factory            = $this->factory;

        return  $this->tx_manager->transaction(function() use ($factory, $validator_factory, $data, $repository){

            $validator = $validator_factory->buildValidatorForJob($data);

            if ($validator->fails()) {
                throw new EntityValidationException($validator->messages());
            }

            $job = new Job();

            $job->registerMainInfo($factory->buildJobMainInfo($data));
            $job->registerPostedDate($data['posted_date']);
            $locations = $factory->buildJobLocations($data);

            $job->clearLocations();

            foreach($locations as $location)
                $job->addLocation($location);

            $repository->add($job);

            return $job;
        });
    }
} 