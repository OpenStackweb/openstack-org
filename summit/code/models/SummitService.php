<?php

/**
 * Copyright 2015 OpenStack Foundation
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
class SummitService
{

    /**
     * @var ISummitRepository
     */
    private $summit_repository;
    /**
     * @var ISummitEventRepository
     */
    private $event_repository;
    /**
     * @var ITransactionManager
     */
    private $tx_service;

    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitEventRepository $event_repository,
        ITransactionManager $tx_service
    )
    {
        $this->summit_repository = $summit_repository;
        $this->event_repository  = $event_repository;
        $this->tx_service        = $tx_service;
    }

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function publishEvent(ISummit $summit, array $event_data)
    {
        $event_repository = $this->event_repository;
        return $this->tx_service->transaction(function() use($summit, $event_data, $event_repository){
            if(!isset($event_data['id'])) throw new EntityValidationException(array('missing required param: id'));
            $event_id = intval($event_data['id']);
            $event = $event_repository->getById($event_id);

            if(is_null($event))
                throw new NotFoundEntityException('Summit Event', sprintf('id %s', $event_id));

            if(intval($event->SummitID) !== intval($summit->getIdentifier()))
                throw new EntityValidationException(array('event doest not belongs to summit'));

            if(!$event->Type()->exists())
                throw new EntityValidationException(array('event doest not have a valid event type'));

            // validate blackout times
            $conflict_events = $event_repository->getPublishedByTimeFrame(intval($event->SummitID),$event_data['start_datetime'],$event_data['end_datetime']);
            foreach ($conflict_events as $c_event) {
                // if the published event is BlackoutTime or if there is a BlackoutTime event in this timeframe
                if (($event->Type()->BlackoutTimes || $c_event->Type()->BlackoutTimes) && $event->ID != $c_event->ID) {
                    throw new EntityValidationException(array("You can't publish on this timeframe, it conflicts with '".$c_event->Title."'"));
                }
                // if trying to publish an event on a slot occupied by another event
                if (intval($event_data['location_id']) == $c_event->LocationID && $event->ID != $c_event->ID) {
                    throw new EntityValidationException(array("You can't publish on this timeframe, it conflicts with '".$c_event->Title."'"));
                }
            }

            $event->setStartDate($event_data['start_datetime']);
            $event->setEndDate($event_data['end_datetime']);
            $event->LocationID = intval($event_data['location_id']);
            $event->unPublish();
            $event->publish();
            return $event;
        });
    }

    /**
     * @param ISummit $summit
     * @param ISummitEvent $event
     * @return mixed
     */
    public function unpublishEvent(ISummit $summit, ISummitEvent $event)
    {
        $event_repository = $this->event_repository;
        return $this->tx_service->transaction(function() use($summit, $event, $event_repository){

            if(intval($event->SummitID) !== intval($summit->getIdentifier()))
                throw new EntityValidationException(array('event doest not belongs to summit'));
            $event->unPublish();
            return $event;
        });
    }
}