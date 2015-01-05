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
 * Class EventManager
 */
final class EventManager {

	/**
	 * @var IEntityRepository
	 */
	private $event_repository;
	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;
	/**
	 * @var IEventRegistrationRequestFactory
	 */
	private $factory;
	/**
	 * @var IGeoCodingService
	 */
	private $geo_coding_service;

	/**
	 * @var IEventPublishingService
	 */
	private $event_publishing_service;

	/**
	 * @var IEventValidatorFactory
	 */
	private $validator_factory;

	/**
	 * @param IEntityRepository                $event_repository
	 * @param IEventRegistrationRequestFactory $factory
	 * @param IGeoCodingService                $geo_coding_service
	 * @param IEventPublishingService          $event_publishing_service
	 * @param IEventValidatorFactory           $validator_factory
	 * @param ITransactionManager              $tx_manager
	 */
	public function __construct(IEntityRepository $event_repository,
	                            IEventRegistrationRequestFactory $factory,
								IGeoCodingService $geo_coding_service,
								IEventPublishingService $event_publishing_service,
								IEventValidatorFactory $validator_factory,
	                            ITransactionManager $tx_manager){
		$this->event_repository                      = $event_repository;
		$this->tx_manager                            = $tx_manager;
		$this->factory                               = $factory;
		$this->geo_coding_service                    = $geo_coding_service;
		$this->event_publishing_service              = $event_publishing_service;
		$this->validator_factory                     = $validator_factory;
	}

    /**
     * @param $id
     * @return IEvent
     */
    public function toggleSummitEvent($id){
        $event_repository          = $this->event_repository;

        $event =  $this->tx_manager->transaction(function() use ($id, $event_repository){
            $event = $event_repository->getById($id);
            if(!$event) throw new NotFoundEntityException('EventPage',sprintf('id %s',$id ));
            $event->toggleSummit();

            return $event;
        });

        return $event;
    }

    /**
     * @param array $data
     * @return IEvent
     */
	public function updateEvent(array $data){

		$this_var           = $this;
		$validator_factory  = $this->validator_factory;
		$repository         = $this->event_repository;
		$factory            = $this->factory;

		return  $this->tx_manager->transaction(function() use ($this_var,$factory, $validator_factory, $data, $repository){
			$validator = $validator_factory->buildValidatorForEvent($data);
			if ($validator->fails()) {
				throw new EntityValidationException($validator->messages());
			}
			$event = $repository->getById(intval($data['id']));
			if(!$event)
				throw new NotFoundEntityException('EventPage',sprintf('id %s',$data['id'] ));

            $event->registerMainInfo($factory->buildEventMainInfo($data));
            $event->registerLocation($data['location']);
            $event->registerDuration($factory->buildEventDuration($data));
		});
	}

    /**
     * @param array $data
     * @return IEvent
     */
    public function addEvent(array $data){

        $this_var           = $this;
        $validator_factory  = $this->validator_factory;
        $repository         = $this->event_repository;
        $factory            = $this->factory;

        return  $this->tx_manager->transaction(function() use ($this_var,$factory, $validator_factory, $data, $repository){
            $validator = $validator_factory->buildValidatorForEvent($data);
            if ($validator->fails()) {
                throw new EntityValidationException($validator->messages());
            }

            $event = new EventPage();

            $event->registerMainInfo($factory->buildEventMainInfo($data));
            $event->registerLocation($data['location']);
            $event->registerDuration($factory->buildEventDuration($data));

            $event_id = $repository->add($event);

            return $event_id;
        });
    }

    /**
     * @param $id
     * @return IEvent
     */
    public function deleteEvent($id){
        $event_repository = $this->event_repository;

        $event =  $this->tx_manager->transaction(function() use ($id, $event_repository){
            $event = $event_repository->getById($id);
            $event_repository->delete($event);

        });

    }

} 