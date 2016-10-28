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
class SummitAppAttendeesApi extends AbstractRestfulJsonApi {


    /**
     * @var IEntityRepository
     */
    private $summit_repository;

    /**
     * @var ISummitEventRepository
     */
    private $summitevent_repository;

    /**
     * @var ISummitAttendeeRepository
     */
    private $summitattendee_repository;

    /**
     * @var ISummitPresentationRepository
     */
    private $summitpresentation_repository;

    /**
     * @var IEventbriteAttendeeRepository
     */
    private $eventbriteattendee_repository;

    /**
     * @var ISummitService
     */
    private $summit_service;

    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitEventRepository $summitevent_repository,
        ISummitAttendeeRepository $summitattendee_repository,
        ISummitPresentationRepository $summitpresentation_repository,
        IEventbriteAttendeeRepository $eventbriteattendee_repository,
        ISummitService $summit_service
    )
    {
        parent::__construct();
        $this->summit_repository             = $summit_repository;
        $this->summitevent_repository        = $summitevent_repository;
        $this->summitattendee_repository     = $summitattendee_repository;
        $this->summitpresentation_repository = $summitpresentation_repository;
        $this->eventbriteattendee_repository = $eventbriteattendee_repository;
        $this->summit_service                = $summit_service;
    }


    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        if(!Permission::check('ADMIN_SUMMIT_APP_FRONTEND_ADMIN')) return false;
        return $this->checkOwnAjaxRequest();
    }

    protected function authenticate() {
        return true;
    }

    static $url_handlers = array(
        'GET '                                               => 'getAttendees',
        'GET unmatched/$EB_ATTENDEE_ID!/suggestions'         => 'getEventbriteUnmatchedSuggestions',
        'GET unmatched'                                      => 'getEventbriteUnmatched',
        'POST match/$EB_ATTENDEE_ID!/$MEMBER_ID!'            => 'matchEventbriteAttendee',
        'GET $ATTENDEE_ID!/schedule'                         => 'getSchedule',
        'PUT $ATTENDEE_ID!/tickets/$TICKET_ID!/reassign'     => 'reassignTicket',
        'GET $ATTENDEE_ID!/tickets/$TICKET_ID!'              => 'getTicketData',
        'DELETE $ATTENDEE_ID!/tickets/$TICKET_ID!'           => 'removeTicket',
        'DELETE $ATTENDEE_ID!/rsvp/$RSVP_ID!'                => 'removeRSVP',
        'PUT $ATTENDEE_ID!'                                  => 'updateAttendee',
        'POST $ATTENDEE_ID!/tickets'                         => 'addTicket',
        'POST '                                              => 'addAttendee',
    );

    static $allowed_actions = array(
        'getAttendees',
        'updateAttendee',
        'reassignTicket',
        'getTicketData',
        'getSchedule',
        'addAttendee',
        'removeTicket',
        'addTicket',
        'removeRSVP',
        'getEventbriteUnmatched',
        'getEventbriteUnmatchedSuggestions',
        'matchEventbriteAttendee',
    );

    public function getAttendees(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : '';
            $page_size    = (isset($query_string['items'])) ? Convert::raw2sql($query_string['items']) : '';
            $search_term  = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            list($attendees,$count) = $this->summitattendee_repository->findAttendeesBySummit($search_term,$page,$page_size,$summit_id);

            $attendees_array = array();

            foreach($attendees as $attendee) {
                $attendees_array[] = array(
                    'id'            => $attendee->ID,
                    'member_id'     => $attendee->MemberID,
                    'name'          => $attendee->Member->FullName,
                    'email'         => $attendee->Member->Email,
                    'eventbrite_id' => $attendee->getTicketIDs(),
                    'ticket_bought' => $attendee->getBoughtDate(),
                    'checked_in'    => $attendee->SummitHallCheckedIn,
                    'link'          => 'summit-admin/'.$summit_id.'/attendees/'.$attendee->ID,
                    'schedule'      => $attendee->Schedule()->toNestedArray()
                );
            }

           return $this->ok(array('attendees' => $attendees_array, 'count' => $count));
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function addAttendee(SS_HTTPRequest $request)
    {
        try
        {
            if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
            $summit_id     = intval($request->param('SUMMIT_ID'));
            $attendee_data = $this->getJsonRequest();
            $summit = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $this->summit_service->addAttendee($summit, $attendee_data);
            return $this->ok();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function updateAttendee(SS_HTTPRequest $request)
    {
        try
        {
            if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
            $summit_id     = intval($request->param('SUMMIT_ID'));
            $attendee_id   = intval($request->param('ATTENDEE_ID'));
            $attendee_data = $this->getJsonRequest();
            $attendee_data['id'] = $attendee_id;
            $summit = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $this->summit_service->updateAttendee($summit, $attendee_data);
            return $this->updated();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getTicketData(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $ticket_id    = intval($request->param('TICKET_ID'));
            $ticket       = SummitAttendeeTicket::get_by_id('SummitAttendeeTicket',$ticket_id);
            if(is_null($ticket)) throw new NotFoundEntityException('SummitAttendeeTicket', sprintf(' id %s', $ticket_id));

            $ticket_array = $ticket->toMap();
            $ticket_array['TicketTypeName'] = $ticket->TicketType()->Name;
            return $this->ok($ticket_array);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function reassignTicket(SS_HTTPRequest $request)
    {
        try
        {
            if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
            $summit_id     = intval($request->param('SUMMIT_ID'));
            $ticket_id   = intval($request->param('TICKET_ID'));
            $ticket_data = $this->getJsonRequest();

            $summit = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $this->summit_service->reassignTicket($summit, $ticket_id, $ticket_data['member']);
            return $this->ok();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getSchedule(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $attendee_id  = intval($request->param('ATTENDEE_ID'));
            $attendee     = SummitAttendee::get_by_id('SummitAttendee',$attendee_id);
            if(is_null($attendee)) throw new NotFoundEntityException('SummitAttendee', sprintf(' id %s', $attendee_id));

            $events_array = array();
            $events = $attendee->Schedule()->sort('StartDate');
            foreach ($events as $event) {
                $event_start_unix = strtotime($event->getStartDateUTC());
                $event_end_unix = strtotime($event->getEndDateUTC());
                $current_event = (time() > $event_start_unix && time() < $event_end_unix);

                $events_array[] = array(
                    'title'    => $event->Title,
                    'location' => $event->getLocationNameNice(),
                    'time'     => $event->getDateNice(),
                    'current_event' => $current_event
                );
            }
            return $this->ok($events_array);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function removeTicket(SS_HTTPRequest $request)
    {
        try
        {
            $summit_id     = intval($request->param('SUMMIT_ID'));
            $ticket_id   = intval($request->param('TICKET_ID'));

            $summit = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $this->summit_service->reassignTicket($summit, $ticket_id, null);
            return $this->ok();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function addTicket(SS_HTTPRequest $request)
    {
        try
        {
            if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
            $summit_id     = intval($request->param('SUMMIT_ID'));
            $attendee_id   = intval($request->param('ATTENDEE_ID'));
            $ticket_data = $this->getJsonRequest();

            $summit = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $this->summit_service->addAttendeeTicket($summit, $attendee_id, $ticket_data);
            return $this->ok();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function removeRSVP(SS_HTTPRequest $request)
    {
        try
        {
            $summit_id     = intval($request->param('SUMMIT_ID'));
            $rsvp_id   = intval($request->param('RSVP_ID'));

            $summit = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            RSVP::delete_by_id('RSVP',$rsvp_id);

            return $this->ok();
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getEventbriteUnmatched(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : '';
            $page_size    = (isset($query_string['items'])) ? Convert::raw2sql($query_string['items']) : '';
            $search_term  = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
            $suggested_only  = (isset($query_string['filter_suggested'])) ? Convert::raw2sql($query_string['filter_suggested']) : 0;
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            list($attendees,$count) = $this->eventbriteattendee_repository->getUnmatchedPaged($search_term, $suggested_only, $page, $page_size);

            $attendees_array = array();

            foreach($attendees as $attendee) {
                $attendees_array[] = array(
                    'name'          => $attendee->FirstName.' '.$attendee->LastName,
                    'email'         => $attendee->Email,
                    'eventbrite_id' => $attendee->ExternalAttendeeId,
                    'amount_paid'   => $attendee->Price,
                    'external_ids'  => $attendee->ExternalIds,
                );
            }

            return $this->ok(array('attendees' => $attendees_array, 'count' => $count));
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getEventbriteUnmatchedSuggestions(SS_HTTPRequest $request){
        try
        {
            $eb_attendee_id  = intval($request->param('EB_ATTENDEE_ID'));
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $eb_attendee = $this->eventbriteattendee_repository->getByAttendeeId($eb_attendee_id);
            if(is_null($eb_attendee)) throw new NotFoundEntityException('Attendee', sprintf(' id %s', $eb_attendee_id));

            $suggestions = $this->eventbriteattendee_repository->getSuggestions($eb_attendee);

            $suggestion_array = array();
            foreach ($suggestions as $suggestion) {
                $suggestion_array[] = array(
                    'id'     => $suggestion->ID,
                    'name'   => $suggestion->FirstName.' '.$suggestion->Surname,
                    'email'  => $suggestion->Email,
                    'reason' => $suggestion->Reason,
                );
            }

            return $this->ok($suggestion_array);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function matchEventbriteAttendee(SS_HTTPRequest $request){
        try
        {
            $eb_attendee_id  = intval($request->param('EB_ATTENDEE_ID'));
            $member_id  = intval($request->param('MEMBER_ID'));
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $attendee = $this->summit_service->matchEventbriteAttendee($summit, $eb_attendee_id, $member_id);

            return $this->ok($attendee->ID);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

}