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
        'GET '    => 'getAttendees',
    );

    static $allowed_actions = array(
        'getAttendees',
    );

    public function getAttendees(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : '';
            $ipp          = (isset($query_string['items'])) ? Convert::raw2sql($query_string['items']) : '';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            if ($page && $ipp) {
                $offset = ($page - 1) * $ipp;
                $attendees = $summit->Attendees()->limit($ipp,$offset);
            } else {
                $attendees = $summit->Attendees();
            }

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


}