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

/**
 * Class SummitAppScheduleApi
 */
final class SummitAppScheduleApi extends AbstractRestfulJsonApi {

    /**
     * @var IEntityRepository
     */
    private $summit_repository;

    /**
     * @var IEntityRepository
     */
    private $summitevent_repository;

    /**
     * @var IEntityRepository
     */
    private $summitpresentation_repository;

    /**
     * @var IEntityRepository
     */
    private $eventfeedback_repository;


    /**
     * @var IEntityRepository
     */
    private $attendee_repository;

    /**
     * @var IScheduleManager
     */
    private $schedule_manager;

    /**
     * @var SecurityToken
     */
    private $securityToken;

    public function __construct()
    {
        parent::__construct();
        // TODO: set by IOC
        $this->securityToken                 = new SecurityToken();
        $this->summit_repository             = new SapphireSummitRepository;
        $this->summitevent_repository        = new SapphireSummitEventRepository();
        $this->summitpresentation_repository = new SapphireSummitPresentationRepository();
        $this->eventfeedback_repository      = new SapphireEventFeedbackRepository();
        $this->attendee_repository           = new SapphireSummitAttendeeRepository();

        $this->schedule_manager = new ScheduleManager($this->summitevent_repository, $this->summitpresentation_repository,
                                                      $this->eventfeedback_repository, new EventFeedbackFactory(),
                                                      $this->attendee_repository, SapphireTransactionManager::getInstance());

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
        return $this->checkOwnAjaxRequest();
    }

    protected function authenticate() {
        return true;
    }

    static $url_handlers = array(
        'PUT $EventID!'             => 'addToSchedule',
        'DELETE $EventID!'          => 'removeFromSchedule',
        'POST $EventID!/feedback'   => 'addFeedback',
    );

    static $allowed_actions = array(
        'addToSchedule',
        'removeFromSchedule',
        'addFeedback',
    );

    public static function isEventOnMySchedule($event_id, Summit $summit)
    {
        $member = Member::currentUser();
        if(is_null($member) || !$member->isAttendee($summit->ID)) return false;
        return $member->getSummitAttendee($summit->ID)->isScheduled(intval($event_id));
    }

    public function addToSchedule() {
        try{
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $event_id  = (int)$this->request->param('EventID');
            $member    = Member::currentUser();

            if(is_null($member)) return $this->permissionFailure();

            if(intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));
            if(strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if(is_null($summit))
                return $this->notFound('summit not found!');

            if(intval($event_id) > 0)
                $event = $this->summitevent_repository->getById(intval($event_id));

            if(is_null($event))
                return $this->notFound('event not found!');
            else if($event->getSummit()->getIdentifier() != intval($summit_id))
                return $this->notFound('event not found in current summit');

            $this->schedule_manager->addEventToSchedule(Member::currentUserID(), $event_id);
        }
        catch(EntityValidationException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function removeFromSchedule() {
        try{
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $event_id  = (int)$this->request->param('EventID');
            $member    = Member::currentUser();

            if(is_null($member)) return $this->permissionFailure();

            if(intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if(strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if(is_null($summit))
                return $this->notFound('summit not found!');

            if(intval($event_id) > 0)
                $event = $this->summitevent_repository->getById(intval($event_id));

            if(is_null($event))
                return $this->notFound('event not found!');
            else if($event->getSummit()->getIdentifier() != intval($summit_id))
                return $this->notFound('event not found in current summit');

            $this->schedule_manager->removeEventFromSchedule(Member::currentUserID(), $event_id);
        }
        catch(EntityValidationException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @return SS_HTTPResponse
     */
    public function addFeedback(){
        try {
            $data      = $this->getJsonRequest();
            $event_id  = (int)$this->request->param('EventID');
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $member_id = Member::CurrentUserID();

            if (!$data) return $this->serverError();

            if(intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if(strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if(is_null($summit))
                return $this->notFound('summit not found!');

            if(intval($event_id) > 0)
                $event = $this->summitevent_repository->getById(intval($event_id));

            if(is_null($event))
                return $this->notFound('event not found!');

            else if($event->getSummit()->getIdentifier() != intval($summit_id))
                return $this->notFound('event not found in current summit');

            $data['summit_id'] = $summit->ID;
            $data['event_id']  = $event_id;
            $data['member_id'] = $member_id;

            return $this->created($this->schedule_manager->addFeedback($data));

        }
        catch(EntityValidationException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }
}