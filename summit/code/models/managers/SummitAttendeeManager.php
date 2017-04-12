<?php

/**
 * Copyright 2017 OpenStack Foundation
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
final class SummitAttendeeManager implements ISummitAttendeeManager
{
    /**
     * @var IMemberRepository
     */
    private $member_repository;
    /**
     * @var ISummitAttendeeRepository
     */
    private $attendee_repository;
    /**
     * @var ISummitAttendeeFactory
     */
    private $attendee_factory;
    /**
     * @var IEventBriteAttendeeRepository
     */
    private $eventbrite_attendee_repository;
    /**
     * @var ITransactionManager
     */
    private $tx_service;
    /**
     * @var IEventbriteEventManager
     */
    private $eventbrite_manager;

    /**
     * SummitAttendeeManager constructor.
     * @param ISummitAttendeeRepository $attendee_repository
     * @param IMemberRepository $member_repository
     * @param ISummitAttendeeFactory $attendee_factory
     * @param ITransactionManager $tx_service
     */
    public function __construct
    (
        ISummitAttendeeRepository $attendee_repository,
        IMemberRepository $member_repository,
        ISummitAttendeeFactory $attendee_factory,
        IEventBriteAttendeeRepository $eventbrite_attendee_repository,
        ITransactionManager $tx_service
    )
    {
        $this->attendee_repository            = $attendee_repository;
        $this->member_repository              = $member_repository;
        $this->attendee_factory               = $attendee_factory;
        $this->eventbrite_attendee_repository = $eventbrite_attendee_repository;
        $this->tx_service                     = $tx_service;
    }



    public function getEventbriteEventManager()
    {
        return $this->eventbrite_manager;
    }

    public function setEventbriteEventManager(IEventbriteEventManager $eventbrite_manager)
    {
        $this->eventbrite_manager = $eventbrite_manager;
    }

    /**
     * @param ISummit $summit
     * @param array $attendee_data
     * @return mixed
     */
    public function addAttendee(ISummit $summit, array $attendee_data)
    {
        $attendee_factory    = $this->attendee_factory;
        $attendee_repository = $this->attendee_repository;
        $member_repository = $this->member_repository;

        return $this->tx_service->transaction(function() use($summit, $attendee_data, $attendee_factory, $attendee_repository, $member_repository){
            $member_id = intval($attendee_data['member_id']);
            $member = $member_repository->getById($member_id);

            if (is_null($member))
                throw new NotFoundEntityException('Member', sprintf('id %s', $member_id));

            $attendee = $attendee_repository->getByMemberAndSummit($member_id,$summit->getIdentifier());

            if ($attendee)
                throw new EntityValidationException('This member is already assigned to another attendee');

            $attendee = $attendee_factory->build($member, $summit);
            $attendee_repository->add($attendee);

            return $attendee;
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
                $current_affiliation->JobTitle =  $attendee_data['aff_title'];
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
        return $this->eventbrite_manager->reassignTicket($summit, $ticket_id, $member_id);
    }

    /**
     * @param ISummit $summit
     * @param $attendee_id
     * @param $data
     * @return mixed
     */
    public function addAttendeeTicket(ISummit $summit, $attendee_id, $data)
    {
        $eventbrite_manager = $this->eventbrite_manager;
        $attendee_repository = $this->attendee_repository;

        return $this->tx_service->transaction(function() use($summit, $attendee_id, $data, $eventbrite_manager, $attendee_repository){

            if(!$attendee_id)
                throw new EntityValidationException('missing required param: attendee id');

            if (!isset($data['external_id']) && !empty($data['external_id']))
                throw new EntityValidationException('missing required param: external id');

            $ticket_type = SummitTicketType::get()->byID($data['ticket_type_id']);

            $ticket = $eventbrite_manager->createTicket($data['external_id'], $data['external_attendee_id'], '', $ticket_type, Member::currentUser());

            $attendee = $attendee_repository->getById($attendee_id);
            $attendee->Tickets()->add($ticket);

            return $attendee;
        });
    }

    /**
     * @param ISummit $summit
     * @param $eb_attendee_id
     * @param int $member_id
     * @return ISummitAttendee
     */
    public function matchEventbriteAttendee(ISummit $summit, $eb_attendee_id, $member_id)
    {
        $attendee_factory = $this->attendee_factory;
        $attendee_repository = $this->attendee_repository;
        $member_repository = $this->member_repository;
        $eventbrite_attendee_repository = $this->eventbrite_attendee_repository;

        return $this->tx_service->transaction(function () use (
            $summit, $eb_attendee_id, $member_id, $attendee_factory, $attendee_repository, $member_repository, $eventbrite_attendee_repository) {

            $eb_attendee = $eventbrite_attendee_repository->getByAttendeeId($eb_attendee_id);
            if(is_null($eb_attendee)) throw new NotFoundEntityException('Attendee', sprintf(' id %s', $eb_attendee_id));

            $member = $member_repository->getById($member_id);
            if(is_null($member)) throw new NotFoundEntityException('Member', sprintf(' id %s', $member_id));

            $attendee = $attendee_repository->getByMemberAndSummit($member_id, $summit->getIdentifier());
            if (!$attendee) {
                $attendee = $attendee_factory->build($member, $summit);
            }

            list($eb_attendees,$count) = $eventbrite_attendee_repository->getByEmail($eb_attendee->Email);

            foreach ($eb_attendees as $eb_ticket) {
                $attendee_ticket = SummitAttendeeTicket::get()->where("ExternalAttendeeId = ".$eb_ticket->ExternalAttendeeId)->first();
                if (!$attendee_ticket) {
                    $attendee_ticket = new SummitAttendeeTicket();
                    $ticket_type = SummitTicketType::get()->where("ExternalId = ".$eb_ticket->ExternalTicketClassId)->first();
                    $external_event = EventbriteEvent::get()->where("ID = ".$eb_ticket->EventbriteOrderId)->first();

                    $attendee_ticket->ExternalOrderId = $external_event->ExternalOrderId;
                    $attendee_ticket->ExternalAttendeeId = $eb_ticket->ExternalAttendeeId;
                    $attendee_ticket->TicketTypeID = ($ticket_type) ? $ticket_type->ID : 0;
                }

                $attendee_ticket->OwnerID = $attendee->ID;
                $attendee_ticket->write();
            }

            return $attendee;

        });
    }
}