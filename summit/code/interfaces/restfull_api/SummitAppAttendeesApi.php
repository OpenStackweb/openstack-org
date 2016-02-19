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

    private $summit_service;

    public function __construct()
    {
        parent::__construct();
        // TODO: set by IOC
        $this->summit_repository             = new SapphireSummitRepository;
        $this->summitevent_repository        = new SapphireSummitEventRepository();
        $this->summitattendee_repository     = new SapphireSummitAttendeeRepository();
        $this->summitpresentation_repository = new SapphireSummitPresentationRepository();
        $this->summit_service                = new SummitService
        (
            $this->summit_repository,
            $this->summitevent_repository,
            $this->summitattendee_repository,
            SapphireTransactionManager::getInstance()
        );
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
        if(!Permission::check('ADMIN')) return false;
        return $this->checkOwnAjaxRequest();
    }

    protected function authenticate() {
        return true;
    }


    static $url_handlers = array(
        'GET '                          => 'getAttendees',
        'PUT $ATTENDEE_ID!/update'      => 'updateAttendee',
        'PUT $TICKET_ID!/update_ticket' => 'updateTicket',
        'GET member/$MEMBER_ID!'        => 'getMemberData',
        'GET company_options'           => 'getCompanySearchOptions',
        'GET ticket/$TICKET_ID!'        => 'getTicketData',
    );

    static $allowed_actions = array(
        'getAttendees',
        'updateAttendee',
        'updateTicket',
        'getMemberData',
        'getCompanySearchOptions',
        'getTicketData',
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

            $attendees = $this->summitattendee_repository->findAttendeesBySummit($search_term,$page,$page_size,$summit_id);

            $attendees_array = array();

            foreach($attendees as $attendee) {
                $attendees_array[] = array(
                    'id'            => $attendee->ID,
                    'member_id'     => $attendee->MemberID,
                    'name'          => $attendee->Member->FullName,
                    'email'         => $attendee->Member->Email,
                    'ticket_bought' => $attendee->Tickets()->TicketBoughtDate,
                    'checked_in'    => $attendee->SummitHallCheckedIn,
                    'link'          => 'summit-admin/'.$summit_id.'/attendees/'.$attendee->ID
                );
            }

            echo json_encode($attendees_array);
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

    public function getMemberData(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $member_id    = intval($request->param('MEMBER_ID'));
            $member       = Member::get_by_id('Member',$member_id);
            if(is_null($member)) throw new NotFoundEntityException('Member', sprintf(' id %s', $member_id));

            $speaker = ($member->Speaker()->ID) ? $member->Speaker()->toMap() : '';

            $affiliation = '';
            if ($affiliation_obj = $member->getCurrentAffiliation()) {
                $affiliation = $affiliation_obj->toMap();
                $affiliation['Company'] = array('id'=>$affiliation_obj->Organization()->ID,'name'=>$affiliation_obj->Organization()->Name);
            }

            echo json_encode(array('speaker'=>$speaker,'affiliation'=>$affiliation));
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

    public function getCompanySearchOptions(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $query        = Convert::raw2sql($query_string['query']);
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $orgs = DB::query(" SELECT O.ID AS id, O.Name AS name FROM Org AS O
                                WHERE O.Name LIKE '{$query}%'
                                ORDER BY O.Name");

            $json_array = array();
            foreach ($orgs as $org) {

                $json_array[] = $org;
            }

            echo json_encode($json_array);
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

    public function getTicketData(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $ticket_id    = intval($request->param('TICKET_ID'));
            $ticket       = SummitAttendeeTicket::get_by_id('SummitAttendeeTicket',$ticket_id);
            if(is_null($ticket)) throw new NotFoundEntityException('SummitAttendeeTicket', sprintf(' id %s', $ticket_id));

            echo json_encode($ticket->toMap());
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

    public function updateTicket(SS_HTTPRequest $request)
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
}