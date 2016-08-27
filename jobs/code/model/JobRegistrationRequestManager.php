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
 * Class JobRegistrationRequestManager
 */
final class JobRegistrationRequestManager implements IJobRegistrationRequestManager {
	/**
	 * @var IEntityRepository
	 */
	private $repository;
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
	 * @param IEntityRepository      $repository
	 * @param IEntityRepository      $jobs_repository
	 * @param IAlertEmailRepository  $email_repository
	 * @param IJobFactory            $factory
	 * @param IJobsValidationFactory $validator_factory
	 * @param IJobPublishingService  $jobs_publishing_service
	 * @param ITransactionManager    $tx_manager
	 */
	public function __construct(IEntityRepository $repository,
	                            IEntityRepository $jobs_repository,
	                            IAlertEmailRepository $email_repository,
	                            IJobFactory $factory,
	                            IJobsValidationFactory $validator_factory,
	                            IJobPublishingService $jobs_publishing_service,
	                            ITransactionManager $tx_manager){

		$this->repository              = $repository;
		$this->jobs_repository         = $jobs_repository;
		$this->email_repository        = $email_repository;
		$this->validator_factory       = $validator_factory;
		$this->factory                 = $factory;
		$this->jobs_publishing_service = $jobs_publishing_service;
		$this->tx_manager              = $tx_manager;
	}

	/**
	 * @param array $data
	 * @return IJob
	 */
	public function registerJobRegistrationRequest(array $data){
		$validator_factory = $this->validator_factory;
		$factory           = $this->factory;
		$repository        = $this->repository ;
		return $this->tx_manager->transaction(function() use($data, $repository, $factory, $validator_factory){
			$validator = $validator_factory->buildValidatorForJobRegistration($data);
			if ($validator->fails()) {
					throw new EntityValidationException($validator->messages());
			}
			$new_registration_request = $factory->buildJobRegistrationRequest(
				$factory->buildJobMainInfo($data),
				$factory->buildJobLocations($data),
				$factory->buildJobPointOfContact($data)
			);
			$current_user = Member::currentUser();
			if($current_user){
				$new_registration_request->registerUser($current_user);
			}
			$repository->add($new_registration_request);
		});
	}

	/**
	 * @param array $data
	 * @return IJobRegistrationRequest
	 */
	public function updateJobRegistrationRequest(array $data){
		$validator_factory = $this->validator_factory;
		$repository        = $this->repository ;
		$factory           = $this->factory;

		return $this->tx_manager->transaction(function() use($data, $repository, $validator_factory, $factory){

			$validator = $validator_factory->buildValidatorForJobRegistration($data);

			if ($validator->fails()) {
				throw new EntityValidationException($validator->messages());
			}

			$request = $repository->getById(intval($data['id']));

			if(!$request)
				throw new NotFoundEntityException('JobRegistrationRequest',sprintf('id %s',$data['id'] ));

			$request->registerMainInfo($factory->buildJobMainInfo($data));

			$locations = $factory->buildJobLocations($data);

			$request->clearLocations();
			foreach($locations as $location)
				$request->registerLocation($location);

			$request->registerPointOfContact($factory->buildJobPointOfContact($data));

			return $request;
		});
	}

	/**
	 * @param $id
	 * @param $jobs_link
	 * @return IJob
	 */
	public function postJobRegistrationRequest($id, $jobs_link){
		$repository               = $this->repository;
		$factory                  = $this->factory;
		$jobs_repository          = $this->jobs_repository;
		$jobs_publishing_service  = $this->jobs_publishing_service ;

		$job =  $this->tx_manager->transaction(function() use ($id, $repository, $jobs_repository, $factory, $jobs_link, $jobs_publishing_service){

			$request = $repository->getById($id);
			if(!$request) throw new NotFoundEntityException('JobRegistrationRequest',sprintf('id %s',$id ));

			$job = $factory->buildJob($request);
            // force write we need the id
            $job->write();
			//$job_id = $jobs_repository->add($job);
			$request->markAsPosted();

			//send Accepted message
			$point_of_contact = $request->getPointOfContact();
			$name_to          = $point_of_contact->getName();
			$email_to         = $point_of_contact->getEmail();

			if(empty($name_to)  || empty($email_to ))
				throw new EntityValidationException(array(array('message'=>'invalid point of contact')));
			$email = EmailFactory::getInstance()->buildEmail(JOB_REGISTRATION_REQUEST_EMAIL_FROM, $email_to, "Your OpenStack Job is Now Live");
			$email->setTemplate('JobRegistrationRequestAcceptedEmail');
			$email->populateTemplate(array(
				'JobLink' => $jobs_link.'view/'.$job->getIdentifier().'/'.$job->getSlug(),
			));
			$email->send();
			return $job;
		});


		return $job;
	}

	/**
	 * @param int $batch_size
	 * @param string $email_alert_to
	 * @param string $details_url
	 */
	public function makeDigest($batch_size = 15, $email_alert_to, $details_url){
		$email_repository = $this->email_repository;
		$repository       = $this->repository;
		$factory          = $this->factory;
		$this->tx_manager->transaction(function() use($batch_size, $email_alert_to, $details_url, $repository , $email_repository, $factory){
			$last_email =  $email_repository->getLastOne();
			$query      = new QueryObject();
			$query->addAndCondition(QueryCriteria::equal('isPosted', 0));
			$query->addAndCondition(QueryCriteria::equal('isRejected', 0));
			if($last_email){
				$query->addAndCondition(QueryCriteria::greater('ID', $last_email->getLastJobRegistrationRequest()->getIdentifier() ));
			}
			$query->addOrder(QueryOrder::asc('PostDate'));
			list($list,$size) = $repository->getAll($query,0,$batch_size);
			if($list && count($list)>0) {
				$last_one = end($list);
				reset($list);
				$email = EmailFactory::getInstance()->buildEmail(JOB_REGISTRATION_REQUEST_EMAIL_FROM, $email_alert_to, "New Job Registration Requests");
				$email->setTemplate('JobAlertEmail');
				$email->populateTemplate(array(
					'RegistrationRequests' => new ArrayList($list),
					'Details'              => $details_url,
				));
				$email->send();
				$email_repository->add($factory->buildJobAlertEmail($last_one));
			}
		});
	}

	/**
	 * @param string $period
	 * @return mixed
	 */
	public function makePurge($period='P1Y') {
		$repository = $this->jobs_repository;
		return $this->tx_manager->transaction(function() use($repository, $period){
			$query = new QueryObject;
			$now   = new DateTime();
			$now   = $now->sub(new DateInterval($period));
			$query->addAndCondition(QueryCriteria::lower('PostedDate',$now->format('Y-m-d H:i:s')));
			list($res,$count) = $repository->getAll($query,0,50);
			foreach($res as $job){
				$repository->delete($job);
			}
		});
	}

    /**
     * @param $id
     * @param array $data
     * @param $jobs_link
     * @return mixed
     */
    public function rejectJobRegistration($id, array $data, $jobs_link){
        $this_var           = $this;
        $validator_factory  = $this->validator_factory;
        $repository         = $this->repository;

        return  $this->tx_manager->transaction(function() use ($this_var, $id, $data, $validator_factory, $repository, $jobs_link){

            $validator = $validator_factory->buildValidatorForJobRejection($data);
            if ($validator->fails()) {
                throw new EntityValidationException($validator->messages());
            }

            $request = $repository->getById(intval($id));
            if(!$request)
                throw new NotFoundEntityException('JobRegistrationRequest',sprintf('id %s',$id ));

            $request->markAsRejected();

            if(@$data['send_rejection_email']){
                //send rejection message
                $point_of_contact = $request->getPointOfContact();
                $name_to  = $point_of_contact->getName();
                $email_to = $point_of_contact->getEmail();
                if(empty($name_to)  || empty($email_to ))
                    throw new EntityValidationException(array(array('message'=>'invalid point of contact')));

                $email = EmailFactory::getInstance()->buildEmail(JOB_REGISTRATION_REQUEST_EMAIL_FROM, $email_to, "Your Recent OpenStack Job Submission");
                $email->setTemplate('JobRegistrationRequestRejectedEmail');
                $email->populateTemplate(array(
                    'JobLink'           => $jobs_link,
                    'JobEmailFrom'      => JOB_REGISTRATION_REQUEST_EMAIL_FROM,
                    'AdditionalComment' => @$data['custom_reject_message']
                ));
                $email->send();
            }
        });
    }
} 