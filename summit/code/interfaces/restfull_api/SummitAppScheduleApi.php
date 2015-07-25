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
    private $speakerfeedback_repository;

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

        $this->securityToken     = new SecurityToken();
        $this->summit_repository  = new SapphireSummitRepository;
        $this->summitevent_repository = new SapphireSummitEventRepository();
        $this->summitpresentation_repository = new SapphireSummitPresentationRepository();
        $this->eventfeedback_repository = new SapphireEventFeedbackRepository();
        $this->speakerfeedback_repository = new SapphireSpeakerFeedbackRepository();
        $this->attendee_repository = new SapphireSummitAttendeeRepository();

        $this->schedule_manager = new ScheduleManager($this->summitevent_repository, $this->summitpresentation_repository,
                                                      $this->eventfeedback_repository, new EventFeedbackFactory(), $this->speakerfeedback_repository,
                                                      $this->attendee_repository, SapphireTransactionManager::getInstance());

        $this_var           = $this;
        $security_token     = $this->securityToken;

        $this->addBeforeFilter('addFeedback','check_access_reject',function ($request) use($this_var, $security_token){
            $data = $this_var->getJsonRequest();
            if (!$data) return $this->serverError();
            if (!$security_token->checkRequest($request)) return $this->forbiddenError();
            if ($data['field_98438688'] != '') return $this->forbiddenError();
        });


    }

    public function checkOwnAjaxRequest($request){
        $referer = @$_SERVER['HTTP_REFERER'];
        if(empty($referer)) return false;
        //validate
        if (!Director::is_ajax()) return false;
        return Director::is_site_url($referer);
    }

    public function checkAdminPermissions($request){
        return true; //Permission::check("SUMMITAPP_ADMIN_ACCESS");
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
        return true;
    }

    protected function authenticate() {
        return true;
    }

    static $url_handlers = array(
        'GET '                       => 'getSchedule',
        'PUT $EventID!'              => 'addToSchedule',
        'DELETE $EventID!'           => 'removeFromSchedule',
        'PUT $EventID!/feedbacks'    => 'addFeedback',
    );

    static $allowed_actions = array(
        'getSchedule',
        'addToSchedule',
        'removeFromSchedule',
        'addFeedback',
    );

    public function getSchedule(SS_HTTPRequest $request) {
        $query_string        = $request->getVars();
        $summit_types        = isset($query_string['summit_types']) ? Convert::raw2sql($query_string['summit_types']) : '';
        $event_type_filter   = isset($query_string['event_type']) ? Convert::raw2sql($query_string['event_type']) : null;
        $summit_types_filter = explode(',', $summit_types);
        $source              = isset($query_string['summit_source']) ? Convert::raw2sql($query_string['summit_source']) : null;
        $summit_id           = $request->param('SUMMIT_ID');
        $summit              = null;

        if(intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));
        if(strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if(is_null($summit))
            return $this->notFound('summit not found!');

        if ($source == 'public') {
            $events = $summit->getSchedule();
        } else {
            $attendee = Member::currentUser()->getSummitAttendee($summit->ID);
            $events = $attendee->getSchedule();
        }

        $filtered_events = array();

        foreach ($events as $event) {
            $summit_types = $event->getAllowedSummitTypes();
            $event_type = $event->Type()->ID;
            $date = date('F j',strtotime($event->StartDate));

            // filter event - I leave this here in case we need to filter from server side, but for now we filter on client side
            /*$skip_event = true;
            if (count($summit_types)) {
                foreach ($summit_types as $summit_type) {
                    if (in_array($summit_type->ID,$summit_types_filter)) $skip_event = false;
                }
            } else {
                $skip_event = false;
            }

            if ($event_type != $event_type_filter && $event_type_filter != '-1') $skip_event = true;
            if ($skip_event) continue;*/
            // end filter event

            $event->Date = $date;
            $event->StartTime = date('g:ia',strtotime($event->StartDate));
            $event->EndTime = date('g:ia',strtotime($event->EndDate));
            $event->EventLink = $event->getLink();
            $event->EventType = $event->Type()->toMap();
            $event->EventCategory = ($event->isPresentation()) ? $event->Category()->toMap() : null;
            $event->EventLocation = $event->getLocationNameNice();
            $event->isScheduledEvent = $event->isScheduled();
            $event->isAttendee = $event->Summit->isAttendee();

            $speakers = array();
            foreach ($event->getSpeakers() as $speaker) {
                $speaker->ProfilePic = $speaker->ProfilePhoto(50);
                $speakers[] = $speaker->toMap();
            }
            $event->EventSpeakers = $speakers;

            if ($event->isPresentation()) {
                $topics = array();
                foreach ($event->getTopics() as $topic) {
                    $topics[] = $topic->toMap();
                }
                $event->EventTopics = $topics;
            }

            $event->SummitTypes = '';
            foreach ($summit_types as $summit_type) {
                $event->SummitTypes .= ' summit_type_'.$summit_type->ID;
            }

            if (!isset($filtered_events[$date])) $filtered_events[$date] = array();

            $filtered_events[$date][] = $event->toMap();
        }

        return $this->ok($filtered_events);
    }

    public function addToSchedule() {
        try{
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $event_id = (int)$this->request->param('EventID');
            $member = Member::currentUser();
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
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function removeFromSchedule() {
        try{
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $event_id = (int)$this->request->param('EventID');
            $member = Member::currentUser();
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
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
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

            $data['event_id'] = $event_id;
            $data['member_id'] = $member_id;

            $feedback = $this->eventfeedback_repository->getFeedback($event_id,$member_id);
            if ($feedback) {
                $this->schedule_manager->updateFeedback($data,$feedback);
                return $this->updated();
            } else {
                return $this->created($this->schedule_manager->addFeedback($data));
            }
        }
        catch (PolicyException $ex2) {
            SS_Log::log($ex2,SS_Log::ERR);
            return $this->validationError($ex2->getMessage());
        }
        catch (EntityValidationException $ex3) {
            SS_Log::log($ex3,SS_Log::ERR);
            return $this->validationError($ex3->getMessages());
        }
    }

}