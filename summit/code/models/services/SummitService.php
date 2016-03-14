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
final class SummitService implements ISummitService
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
     * @var ISummitAssistanceRepository
     */
    private $assistance_repository;
    /**
     * @var ITransactionManager
     */
    private $tx_service;

    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitEventRepository $event_repository,
        ISummitAttendeeRepository $attendee_repository,
        ISummitAssistanceRepository $assistance_repository,
        ITransactionManager $tx_service
    )
    {
        $this->summit_repository = $summit_repository;
        $this->event_repository  = $event_repository;
        $this->attendee_repository  = $attendee_repository;
        $this->assistance_repository  = $assistance_repository;
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
        $this_var         = $this;

        return $this->tx_service->transaction(function() use($summit, $this_var, $event_data, $event_repository){

            if(!isset($event_data['id'])) throw new EntityValidationException('missing required param: id');
            $event_id = intval($event_data['id']);
            $event = $event_repository->getById($event_id);

            if(is_null($event))
                throw new NotFoundEntityException('Summit Event', sprintf('id %s', $event_id));

            if(intval($event->SummitID) !== intval($summit->getIdentifier()))
                throw new EntityValidationException('event doest not belongs to summit');

            if(!$event->Type()->exists())
                throw new EntityValidationException('event doest not have a valid event type');

            $event->setStartDate($event_data['start_datetime']);
            $event->setEndDate($event_data['end_datetime']);
            $event->LocationID = intval($event_data['location_id']);
            $this_var->validateBlackOutTimesAndTimes($event);
            $event->unPublish();
            $event->publish();
            return $event;
        });
    }

    /**
     * @param SummitEvent $event
     * @throws EntityValidationException
     */
    public function validateBlackOutTimesAndTimes(SummitEvent $event)
    {
        // validate blackout times and speaker conflict
        $event_on_timeframe = $this->event_repository->getPublishedByTimeFrame(intval($event->SummitID), $event->getStartDate(), $event->getEndDate());
        foreach ($event_on_timeframe as $c_event) {
            // if the published event is BlackoutTime or if there is a BlackoutTime event in this timeframe
            if (!$event->Location()->overridesBlackouts() && ($event->Type()->BlackoutTimes || $c_event->Type()->BlackoutTimes) && $event->ID != $c_event->ID) {
                throw new EntityValidationException
                (
                    sprintf
                    (
                        "You can't publish Event (%s) %s  on this timeframe, it conflicts with (%s) %s.",
                        $event->ID,
                        $event->Title,
                        $c_event->ID,
                        $c_event->Title
                    )
                );
            }
            // if trying to publish an event on a slot occupied by another event
            if (intval($event->LocationID) == $c_event->LocationID && $event->ID != $c_event->ID) {
                throw new EntityValidationException
                (
                    sprintf
                    (
                        "You can't publish Event (%s) %s  on this timeframe, it conflicts with (%s) %s.",
                        $event->ID,
                        $event->Title,
                        $c_event->ID,
                        $c_event->Title
                    )
                );
            }

            // validate speaker conflict
            if ($event instanceof Presentation && $c_event instanceof Presentation && $event->ID != $c_event->ID) {
                foreach ($event->Speakers() as $speaker) {
                    if ($c_event->Speakers()->find('ID', $speaker->ID)) {
                        throw new EntityValidationException
                        (
                            sprintf
                            (
                                "You can't publish Event %s (%s) on this timeframe, speaker %s its presention in room %s at this time.",
                                $event->Title,
                                $event->ID,
                                $speaker->getName(),
                                $c_event->getLocationName()
                            )
                        );
                    }
                }
            }
        }
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
                throw new EntityValidationException(EntityValidationException::buildMessage('event doest not belongs to summit'));
            $event->unPublish();
            return $event;
        });
    }

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function createEvent(ISummit $summit, array $event_data)
    {
        $event_repository = $this->event_repository;
        $this_var         = $this;

        return $this->tx_service->transaction(function() use($this_var, $summit, $event_data, $event_repository){

            $event_type_id = intval($event_data['event_type']);
            $event_type    = SummitEventType::get()->byID($event_type_id);
            if(is_null($event_type)) throw new NotFoundEntityException('EventType');

            $event = ($event_type->Type == 'Presentation'|| $event_type->Type == 'Keynotes') ? new Presentation :  new SummitEvent;
            $start_date = $event_data['start_date'];
            $end_date   = $event_data['end_date'];
            if(!empty($start_date) || !empty($end_date))
            {
                $d1 = new DateTime($start_date);
                $d2 = new DateTime($end_date);
                if($d1 >= $d2) throw new EntityValidationException('Start Date should be lower than End Date!');
                if(!$summit->belongsToDuration($d1))
                    throw new EntityValidationException('Start Date should be inside Summit Duration!');
                if(!$summit->belongsToDuration($d2))
                    throw new EntityValidationException('Start Date should be inside Summit Duration!');
            }

            $event->SummitID         = $summit->getIdentifier();
            $event->Title            = $event_data['title'];
            $event->RSVPLink         = $event_data['rsvp_link'];
            $event->HeadCount        = intval($event_data['headcount']);
            $event->ShortDescription = $event_data['short_description'];
            $event->setStartDate($event_data['start_date']);
            $event->setEndDate($event_data['end_date']);
            $event->AllowFeedBack    = $event_data['allow_feedback'];
            $event->LocationID       = intval($event_data['location_id']);
            $event->TypeID           = intval($event_data['event_type']);

            $summit_types = ($event_data['summit_type']) ? $event_data['summit_type'] : array();
            $event->AllowedSummitTypes()->setByIDList($summit_types);
            $tags = ($event_data['tags']) ? explode(',',$event_data['tags']) : array();
            $event->Tags()->setByIDList($tags);
            $sponsors = ($event_data['sponsors']) ? explode(',',$event_data['sponsors']) : array();
            $event->Sponsors()->setByIDList($sponsors);

            self::updatePresentation($event, $event_data);

            $must_publish= (bool)$event_data['publish'];
            if($must_publish)
            {
                $this_var->validateBlackOutTimesAndTimes($event);
                $event->publish();
            }
            $event->write();
            return $event;
        });
    }

    /**
     * @param ISummitEvent $event
     * @param array $event_data
     * @return ISummitEvent
     * @throws NotFoundEntityException
     * @throws ValidationException
     * @throws null
     */
    public static function updatePresentation(ISummitEvent $event, array $event_data)
    {
        // Speakers, if one of the added members is not a speaker, we need to make him one
        if ($event instanceof Presentation) {
            foreach ($event_data['speakers'] as $speaker) {
                if(!isset($speaker['speaker_id']) || !isset($speaker['member_id']))
                    throw new EntityValidationException('missing parameter on speakers collection!');

                $speaker_id = intval($speaker['speaker_id']);
                $member_id  = intval($speaker['member_id']);
                $speaker    = $speaker_id > 0 ? PresentationSpeaker::get()->byID($speaker_id):null;
                $speaker    = is_null($speaker) && $member_id > 0 ? PresentationSpeaker::get()->filter('MemberID', $member_id)->first() : $speaker;

                if (is_null($speaker)) {
                    $member  = Member::get()->byID($member_id);
                    if(is_null($member)) throw new NotFoundEntityException('Member', sprintf(' member id %s', $member_id));
                    $speaker = new PresentationSpeaker();
                    $speaker->FirstName = $member->FirstName;
                    $speaker->LastName = $member->Surname;
                    $speaker->MemberID = $member->ID;
                    $speaker->write();
                }

                $speaker_ids[] = $speaker->ID;
            }

            $event->Speakers()->setByIDList($speaker_ids);

            if($event->Type()->Type == 'Keynotes')
            {
                if(!isset($event_data['moderator']))
                    throw new EntityValidationException('moderator is required!');
                $moderator    = $event_data['moderator'];
                if(!isset($moderator['member_id']) || !isset($moderator['speaker_id']))
                    throw new EntityValidationException('missing parameter on moderator!');

                $speaker_id = intval($moderator['speaker_id']);
                $member_id  = intval($moderator['member_id']);
                $moderator    = $speaker_id > 0 ? PresentationSpeaker::get()->byID($speaker_id):null;
                $moderator    = is_null($moderator) && $member_id > 0 ? PresentationSpeaker::get()->filter('MemberID', $member_id)->first() : null;
                if (is_null($moderator)) {
                    $member  = Member::get()->byID($member_id);
                    if(is_null($member)) throw new NotFoundEntityException('Member', sprintf(' member id %s', $member_id));
                    $moderator = PresentationSpeaker::create();
                    $moderator->FirstName = $member->FirstName;
                    $moderator->LastName  = $member->Surname;
                    $moderator->MemberID  = $member->ID;
                    $moderator->write();
                }
                $event->ModeratorID = $moderator->ID;
            }

            $track = PresentationCategory::get()->byID(intval($event_data['track']));
            if(is_null($track)) throw new NotFoundEntityException('Track');

            $event->CategoryID = $track->ID;
            $event->AttendeesExpectedLearnt = $event_data['expect_learn'];
        }
        return $event;
    }

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function updateEvent(ISummit $summit, array $event_data)
    {
        $event_repository = $this->event_repository;
        $this_var         = $this;

        return $this->tx_service->transaction(function() use($this_var, $summit, $event_data, $event_repository){

            if(!isset($event_data['id'])) throw new EntityValidationException('missing required param: id');

            $event_id = intval($event_data['id']);
            $event    = $event_repository->getById($event_id);

            if(is_null($event))
                throw new NotFoundEntityException('Summit Event', sprintf('id %s', $event_id));

            if(intval($event->SummitID) !== intval($summit->getIdentifier()))
                throw new EntityValidationException('event doest not belongs to summit');

            $start_date = $event_data['start_date'];
            $end_date   = $event_data['end_date'];
            if(!empty($start_date) || !empty($end_date))
            {
                $d1 = new DateTime($start_date);
                $d2 = new DateTime($end_date);
                if($d1 >= $d2) throw new EntityValidationException('Start Date should be lower than End Date!');
                if(!$summit->belongsToDuration($d1))
                    throw new EntityValidationException('Start Date should be inside Summit Duration!');
                if(!$summit->belongsToDuration($d2))
                    throw new EntityValidationException('Start Date should be inside Summit Duration!');
            }

            $event->Title            = $event_data['title'];
            $event->RSVPLink         = $event_data['rsvp_link'];
            $event->HeadCount        = intval($event_data['headcount']);
            $event->ShortDescription = $event_data['short_description'];
            $event->setStartDate($event_data['start_date']);
            $event->setEndDate($event_data['end_date']);
            $event->AllowFeedBack    = $event_data['allow_feedback'];
            $event->LocationID       = intval($event_data['location_id']);

            $summit_types = ($event_data['summit_type']) ? $event_data['summit_type'] : array();
            $event->AllowedSummitTypes()->setByIDList($summit_types);
            $tags = ($event_data['tags']) ? explode(',',$event_data['tags']) : array();
            $event->Tags()->setByIDList($tags);
            $sponsors = ($event_data['sponsors']) ? explode(',',$event_data['sponsors']) : array();
            $event->Sponsors()->setByIDList($sponsors);

            self::updatePresentation($event, $event_data);

            $must_publish= (bool)$event_data['publish'];
            if($event->isPublished() || $must_publish)
            {
                $this_var->validateBlackOutTimesAndTimes($event);
                $event->unPublish();
                $event->publish();
            }
            $event->write();
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

            if(!isset($attendee_data['id']))
                throw new EntityValidationException('missing required param: id');

            $member_id = $attendee_data['member'];
            $attendee_id = intval($attendee_data['id']);

            $attendee = $attendee_repository->getByMemberAndSummit($member_id,$summit->getIdentifier());
            if ($attendee && $attendee->ID != $attendee_id)
                throw new EntityValidationException('This member is already assigned to another tix');

            $attendee = $attendee_repository->getById($attendee_id);

            if(is_null($attendee))
                throw new NotFoundEntityException('Summit Attendee', sprintf('id %s', $attendee_id));

            if(intval($attendee->SummitID) !== intval($summit->getIdentifier()))
                throw new EntityValidationException('attendee doest not belong to summit');

            $attendee->MemberID = $member_id;
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

            if ($attendee->Member()) {
                $current_affiliation = $attendee->Member()->getcurrentAffiliation();
                if (!$current_affiliation) {
                    $current_affiliation = new Affiliation();
                }

                $current_affiliation->OrganizationID =  $attendee_data['aff_company'];
                $current_affiliation->StartDate =  $attendee_data['aff_from'];
                $current_affiliation->EndDate =  $attendee_data['aff_to'];
                $current_affiliation->Current =  $attendee_data['aff_current'];
                $current_affiliation->write();

                $attendee->Member()->Affiliations()->add($current_affiliation);
            }

            return $attendee;
        });
    }

    /**
     * @param ISummit $summit
     * @param $ticket_id
     * @param $member_id
     * @return mixed
     */
    public function reassignTicket(ISummit $summit, $ticket_id, $member_id)
    {
        $attendee_repository = $this->attendee_repository;

        return $this->tx_service->transaction(function() use($summit, $ticket_id, $member_id, $attendee_repository){

            if(!$ticket_id)
                throw new EntityValidationException('missing required param: id');

            $attendee = $attendee_repository->getByMemberAndSummit($member_id,$summit->getIdentifier());
            $ticket = SummitAttendeeTicket::get_by_id('SummitAttendeeTicket',$ticket_id);
            $previous_owner = $ticket->Owner();


            if ($attendee) {
                if ($attendee->Tickets()->count() > 0) {
                    throw new EntityValidationException('This member is already assigned to another tix');
                } else {
                    $previous_owner->Tickets()->remove($ticket);
                    $attendee->Tickets()->add($ticket);
                }
            } else {
                $previous_owner->Tickets()->remove($ticket);

                $attendee = new SummitAttendee();
                $attendee->MemberID = $member_id;
                $attendee->SummitID = $summit->getIdentifier();

                $attendee->Tickets()->add($ticket);
            }

            $attendee->write();

            // if the attendee has no more tickets we delete it
            if ($previous_owner->Tickets()->count() == 0) {
                $previous_owner->delete();
            }

            return $attendee;
        });
    }

    /**
     * @param ISummit $summit
     * @param array $data
     */
    public function updateAndPublishBulkEvents(ISummit $summit, array $data)
    {
        $event_repository = $this->event_repository;
        $this_var         = $this;

        $this->tx_service->transaction(function() use($summit, $data, $event_repository, $this_var){

            $events = $data['events'];
            foreach($events as $event_dto) {
                $event = $event_repository->getById($event_dto['id']);
                if(is_null($event)) throw new NotFoundEntityException('SummitEvent');
                if(intval($event->SummitID) !== $summit->getIdentifier()) throw new EntityValidationException('SummitEvent does not belong to Summit!');

                $event->LocationID = intval($event_dto['location_id']);
                $event->setStartDate(sprintf("%s %s", $event_dto['start_date'], $event_dto['start_time']));
                $event->setEndDate(sprintf("%s %s", $event_dto['end_date'], $event_dto['end_time']));
                $this_var->validateBlackOutTimesAndTimes($event);
                $event->unPublish();
                $event->publish();
                $event->write();
            }
        });
    }

    /**
     * @param ISummit $summit
     * @param array $data
     */
    public function updateBulkEvents(ISummit $summit, array $data)
    {
        $event_repository = $this->event_repository;

        $this->tx_service->transaction(function() use($summit, $data, $event_repository){

            $events = $data['events'];
            foreach($events as $event_dto) {
                $event = $event_repository->getById($event_dto['id']);
                if(is_null($event)) throw new NotFoundEntityException('SummitEvent');
                if(intval($event->SummitID) !== $summit->getIdentifier()) throw new EntityValidationException('SummitEvent does not belong to Summit!');

                $event->LocationID = intval($event_dto['location_id']);
                $event->setStartDate(sprintf("%s %s", $event_dto['start_date'], $event_dto['start_time']));
                $event->setEndDate(sprintf("%s %s", $event_dto['end_date'], $event_dto['end_time']));
                $event->unPublish();
                $event->write();
            }
        });
    }

    /**
     * @param ISummit $summit
     * @param array $event_ids
     */
    public function unPublishBulkEvents(ISummit $summit, array $event_ids)
    {
        $event_repository = $this->event_repository;

        $this->tx_service->transaction(function() use($summit, $event_ids, $event_repository) {
            foreach ($event_ids as $event_id) {
                $event = $event_repository->getById($event_id);
                if (is_null($event)) throw new NotFoundEntityException('SummitEvent');
                if (intval($event->SummitID) !== $summit->getIdentifier()) throw new EntityValidationException('SummitEvent does not belong to Summit!');
                $event->unPublish();
            }
        });
    }

    /**
     * @param ISummit $summit
     * @param array $data
     * @return mixed
     */
    public function updateAssistance(ISummit $summit, array $data)
    {
        $assistance_repository = $this->assistance_repository;
        $this->tx_service->transaction(function() use($summit, $data, $assistance_repository){

            foreach ($data as $assistance_data) {
                if(!isset($assistance_data['id']))
                    throw new EntityValidationException('missing required param: id');

                $assistance_id = intval($assistance_data['id']);

                $assistance = $assistance_repository->getById($assistance_id);

                if(is_null($assistance))
                    throw new NotFoundEntityException('Speaker Assistance', sprintf('id %s', $assistance_id));

                if(intval($assistance->SummitID) !== intval($summit->getIdentifier()))
                    throw new EntityValidationException('speaker assistance doest not belong to summit');

                $assistance->OnSitePhoneNumber = $assistance_data['phone'];
                $assistance->RegisteredForSummit = $assistance_data['registered'];
                $assistance->CheckedIn = $assistance_data['checked_in'];

                $assistance->write();
            }

        });
    }

}