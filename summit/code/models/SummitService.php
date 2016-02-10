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
     * @var ISummitAttendeeRepository
     */
    private $attendee_repository;
    /**
     * @var ITransactionManager
     */
    private $tx_service;

    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitEventRepository $event_repository,
        ISummitAttendeeRepository $attendee_repository,
        ITransactionManager $tx_service
    )
    {
        $this->summit_repository = $summit_repository;
        $this->event_repository  = $event_repository;
        $this->attendee_repository  = $attendee_repository;
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

            // validate blackout times and speaker conflict
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
                // validate speaker conflict
                foreach ($event->Speakers() as $speaker) {
                    if ($c_event->Speakers()->find('ID',$speaker->ID) && $event->ID != $c_event->ID) {
                        throw new EntityValidationException(array("You can't publish on this timeframe, ".$speaker->getName()." is presenting in room '".$c_event->getLocationName()."' at this time."));
                    }
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

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function updateEvent(ISummit $summit, array $event_data)
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

            $event->Title = $event_data['title'];
            $event->Description = $event_data['description'];
            $event->setStartDate($event_data['start_date']);
            $event->setEndDate($event_data['end_date']);
            $event->AllowFeedBack = $event_data['allow_feedback'];
            $event->LocationID = intval($event_data['location_id']);
            $event->TypeID = intval($event_data['event_type']);

            $event->AllowedSummitTypes()->setByIDList($event_data['summit_type']);
            $event->Tags()->setByIDList(explode(',',$event_data['tags']));
            $event->Sponsors()->setByIDList(explode(',',$event_data['sponsors']));

            // Speakers, if one of the added members is not a speaker, we need to make him one
            if ($event->isPresentation()) {
                $presentation = $event_repository->getPresentationById($event_id);
                $speaker_ids = array();
                $member_ids = explode(',',$event_data['speakers']);
                foreach ($member_ids as $member_id) {
                    $speaker = PresentationSpeaker::get()->filter('MemberID', $member_id)->first();

                    if (!$speaker) {
                        $member = Member::get()->byID($member_id);
                        $speaker = new PresentationSpeaker();
                        $speaker->FirstName = $member->FirstName;
                        $speaker->LastName = $member->Surname;
                        $speaker->MemberID = $member->ID;
                        $speaker->write();
                    }

                    $speaker_ids[] = $speaker->ID;
                }

                $event->Speakers()->setByIDList($speaker_ids);
            }

            return $event;
        });
    }

    /**
     * @param ISummit $summit
     * @param array $attendee_data
     * @return mixed
     */
    public function updateAttendee(ISummit $summit, array $attendee_data)
    {
        $attendee_repository = $this->attendee_repository;
        return $this->tx_service->transaction(function() use($summit, $attendee_data, $attendee_repository){
            if(!isset($attendee_data['id'])) throw new EntityValidationException(array('missing required param: id'));
            $attendee_id = intval($attendee_data['id']);
            $attendee = $attendee_repository->getById($attendee_id);

            if(is_null($attendee))
                throw new NotFoundEntityException('Summit Attendee', sprintf('id %s', $attendee_id));

            if(intval($attendee->SummitID) !== intval($summit->getIdentifier()))
                throw new EntityValidationException(array('attendee doest not belong to summit'));

            $attendee->MemberID = $attendee_data['member'];
            $attendee->SharedContactInfo = $attendee_data['share_info'];
            if ($attendee_data['checked_in']) {
                $attendee->registerSummitHallChecking();
            } else {
                $attendee->SummitHallCheckedIn = $attendee_data['checked_in'];
            }

            if ($attendee->Member() && $attendee->Member()->Speaker()->ID) {
                $attendee->Member()->Speaker()->Title = $attendee_data['title'];
                $attendee->Member()->Speaker()->FirstName = $attendee_data['first_name'];
                $attendee->Member()->Speaker()->LastName = $attendee_data['last_name'];
                $attendee->Member()->Speaker()->Bio = $attendee_data['bio'];
                $attendee->Member()->Speaker()->write();
            }

            return $attendee;
        });
    }
}