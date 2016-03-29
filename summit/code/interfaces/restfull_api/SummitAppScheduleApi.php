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
        'GET '                      => 'getScheduleByDay',
        'GET level'                 => 'getScheduleByLevel',
        'GET track'                 => 'getScheduleByTrack',
        'GET search'                => 'getSearchResults',
        'GET empty_spots'           => 'getEmptySpots',
        'PUT $EventID!'             => 'addToSchedule',
        'DELETE $EventID!'          => 'removeFromSchedule',
        'POST $EventID!/feedback'   => 'addFeedback',
        'POST /shareEmail'          => 'shareEmail',
    );

    static $allowed_actions = array(
        'getScheduleByDay',
        'getScheduleByLevel',
        'getScheduleByTrack',
        'getSearchResults',
        'getEmptySpots',
        'addToSchedule',
        'removeFromSchedule',
        'addFeedback',
        'shareEmail',
    );

    protected function getCacheKey(SS_HTTPRequest $request){
        $key    = parent::getCacheKey($request);
        $key   .= '.'.Member::currentUserID();
        return $key;
    }

    public function getScheduleByTrack(SS_HTTPRequest $request)
    {
        $query_string        = $request->getVars();
        $summit_id           = intval($request->param('SUMMIT_ID'));
        $track               = isset($query_string['track']) ? Convert::raw2sql($query_string['track']) : null;
        $cache               = isset($query_string['cache']) ? (bool)Convert::raw2sql($query_string['cache']) : true;
        $summit              = null;

        $member = Member::currentUser();
        $cache  = ($cache && !is_null($member) && $member->isAttendee($summit_id)) ? false: $cache;

        if($cache && $response = $this->loadJSONResponseFromCache($request)) {
            return $response;
        }

        if(intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));
        if(strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if(is_null($summit))
            return $this->notFound('summit not found!');

        $schedule  = $summit->getScheduleByTrack($track);
        $events = $this->normalizeEvents($schedule, $summit);

        $data = array( 'track' => $track, 'events' => $events);
        return $this->saveJSONResponseToCache($request, $data)->ok($data);
    }

    public function getScheduleByLevel(SS_HTTPRequest $request)
    {
        $query_string        = $request->getVars();
        $summit_id           = intval($request->param('SUMMIT_ID'));
        $level               = isset($query_string['level']) ? Convert::raw2sql($query_string['level']) : null;
        $cache               = isset($query_string['cache']) ? (bool)Convert::raw2sql($query_string['cache']) : true;
        $summit              = null;

        $member = Member::currentUser();
        $cache  = ($cache && !is_null($member) && $member->isAttendee($summit_id)) ? false: $cache;

        if($cache && $response = $this->loadJSONResponseFromCache($request)) {
            return $response;
        }

        if(intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));
        if(strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if(is_null($summit))
            return $this->notFound('summit not found!');

        $schedule  = $summit->getScheduleByLevel($level);
        $events = $this->normalizeEvents($schedule, $summit);

        $data = array( 'level' => $level, 'events' => $events);
        return $this->saveJSONResponseToCache($request, $data)->ok($data);
    }

    public function getScheduleByDay(SS_HTTPRequest $request)
    {
        $query_string        = $request->getVars();
        $summit_id           = intval($request->param('SUMMIT_ID'));
        $day                 = isset($query_string['day']) ? Convert::raw2sql($query_string['day']) : null;
        $location            = isset($query_string['location']) ? intval(Convert::raw2sql($query_string['location'])) : null;
        $inc_blackouts       = isset($query_string['blackouts']) ? intval(Convert::raw2sql($query_string['blackouts'])) : null;
        $cache               = isset($query_string['cache']) ? (bool)Convert::raw2sql($query_string['cache']) : true;
        $summit              = null;

        $member = Member::currentUser();
        $cache  = ($cache && !is_null($member) && $member->isAttendee($summit_id)) ? false: $cache;

        if($cache && $response = $this->loadJSONResponseFromCache($request)) {
            return $response;
        }

        if(intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));
        if(strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if(is_null($summit))
            return $this->notFound('summit not found!');

        $schedule  = $summit->getSchedule($day, $location);
        $blackouts = ($inc_blackouts) ? $summit->getBlackouts($day,$location) : new ArrayList();

        $schedule->merge($blackouts);

        $events = $this->normalizeEvents($schedule, $summit);

        $data = array( 'day' => $day, 'events' => $events);
        return $this->saveJSONResponseToCache($request, $data)->ok($data);
    }

    public function normalizeEvents($schedule, $summit) {
        $events    = array();

        foreach($schedule as $e)
        {
            $entry = array
            (
                'id'                       => intval($e->ID),
                'title'                    => $e->Title,
                'description'              => $e->Description,
                'class_name'               => $e->ClassName,
                'abstract'                 => $e->ShortDescription,
                'start_datetime'           => $e->StartDate,
                'end_datetime'             => $e->EndDate,
                'start_time'               => $e->StartTime,
                'end_time'                 => $e->EndTime,
                'date_nice'                => date('D j',strtotime($e->StartDate)),
                'allow_feedback'           => $e->AllowFeedBack,
                'location_id'              => intval($e->LocationID),
                'type_id'                  => intval($e->TypeID),
                'rsvp_link'                => $e->RSVPLink,
                'sponsors_id'              => array(),
                'summit_types_id'          => array(),
                'category_group_ids'       => array(),
                'tags_id'                  => array(),
                'own'                      => self::isEventOnMySchedule($e->ID, $summit),
                'favorite'                 => false,
                'show'                     => true,
                'headcount'                => intval($e->HeadCount),
                'attendees_schedule_count' => $e->AttendeesScheduleCount()
            );

            foreach($e->Tags() as $t)
            {
                array_push($entry['tags_id'], intval($t->ID));
            }

            foreach($e->AllowedSummitTypes() as $t)
            {
                array_push($entry['summit_types_id'], intval($t->ID));
            }

            if($e instanceof Presentation && $e->Category()->exists())
            {
                foreach ($e->Category()->getCategoryGroups() as $group) {
                    array_push($entry['category_group_ids'], intval($group->ID));
                }
            }

            foreach($e->Sponsors() as $e)
            {
                array_push($entry['sponsors_id'], intval($e->ID));
            }

            if($e instanceof Presentation)
            {
                $speakers = array();
                foreach($e->Speakers() as $s)
                {
                    array_push($speakers, intval($s->ID));
                }

                $entry['speakers_id']  = $speakers;
                $entry['moderator_id'] = intval($e->ModeratorID);
                $entry['track_id']     = intval($e->CategoryID);
                $entry['level']        = $e->Level;
                $entry['status']       = $e->SelectionStatus();
            }
            array_push($events, $entry);
        };

        return $events;
    }

    public function getSearchResults(SS_HTTPRequest $request) {

        $query_string        = $request->getVars();
        $summit_id           = intval($request->param('SUMMIT_ID'));
        $term                = isset($query_string['term']) ? Convert::raw2sql($query_string['term']) : null;
        $summit              = null;

        if(intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));
        if(strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if(is_null($summit))
            return $this->notFound('summit not found!');

        $events = array();

        foreach($this->summitevent_repository->searchBySummitAndTerm($summit,$term) as $e)
        {
            $entry = array
            (
                'id'                 => $e->ID,
                'title'              => $e->Title,
                'description'        => $e->Description,
                'short_desc'         => $e->ShortDescription,
                'start_date'         => date('Y-m-d',strtotime($e->StartDate)),
                'start_datetime'     => $e->StartDate,
                'end_datetime'       => $e->EndDate,
                'start_time'         => $e->StartTime,
                'end_time'           => $e->EndTime,
                'allow_feedback'     => $e->AllowFeedBack,
                'location_id'        => $e->LocationID,
                'type_id'            => $e->TypeID,
                'sponsors_id'        => array(),
                'summit_types_id'    => array(),
                'category_group_ids' => array(),
                'tags_id'            => array(),
                'own'                => self::isEventOnMySchedule($e->ID, $summit),
                'favorite'           => false,
                'show'               => true
            );

            foreach($e->Tags() as $t)
            {
                array_push($entry['tags_id'], $t->ID);
            }

            foreach($e->AllowedSummitTypes() as $t)
            {
                array_push($entry['summit_types_id'], $t->ID);
            }

            if ($e->isPresentation()) {
                if($e->Category())
                {
                    foreach ($e->Category()->getCategoryGroups() as $group) {
                        array_push($entry['category_group_ids'], $group->ID);
                    }
                }
            }

            foreach($e->Sponsors() as $e)
            {
                array_push($entry['sponsors_id'], $e->ID);
            }

            if($e instanceof Presentation)
            {
                $speakers = array();
                foreach($e->Speakers() as $s)
                {
                    array_push($speakers, $s->ID);
                }

                $entry['speakers_id']  = $speakers;
                $entry['moderator_id'] = $e->ModeratorID;
                $entry['track_id']     = $e->CategoryID;
                $entry['level']        = $e->Level;
            }
            array_push($events, $entry);
        };
        return $this->ok(array('events' => $events));
    }

    public function getEmptySpots(SS_HTTPRequest $request) {

        $query_string        = $request->getVars();
        $summit_id           = intval($request->param('SUMMIT_ID'));
        $days                = isset($query_string['days']) ? json_decode($query_string['days']) : null;
        $start_time          = isset($query_string['start_time']) ? $query_string['start_time'] : '07:00:00';
        $end_time            = isset($query_string['end_time']) ? $query_string['end_time'] : '22:00:00';
        $locations           = isset($query_string['locations']) ? json_decode($query_string['locations']) : null;
        $length              = isset($query_string['length']) ? intval($query_string['length']) : 0;
        $summit              = null;

        if(intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));
        if(strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if(is_null($summit))
            return $this->notFound('summit not found!');

        if (empty($days) || empty($locations) || empty($length))
            return $this->validationError('Parameters missing.');

        $events = $this->summitevent_repository->getPublishedByTimeAndVenue($summit,$days,$start_time,$end_time,$locations);
        $empty_spots = array();
        $control = array();
        $previous_event = $previous_end_date = $previous_event_day = $previous_event_time = null;

        if ($events) {
            foreach($events as $event) {
                $event_start_date = $event->getStartDate();
                $event_day        = date('Y-m-d',strtotime($event_start_date));

                $control[$event_day][$event->LocationID] = true; //control array to see what days and locations had no events at all

                // if first event or first on this venue or first of the day we use the lower limit
                if ($previous_event == null) {
                    $previous_time = $start_time;
                } else if ($previous_event->LocationID != $event->LocationID || $previous_event_day != $event_day) {
                    $previous_time = $start_time;

                    // if it is the first event of the room/day, add empty spot between last event and top limit
                    $end_limit = $previous_event_day.' '.$end_time;
                    $unix_time = strtotime($previous_end_date);
                    $empty_space = strtotime($end_limit) - $unix_time;
                    if ($empty_space >= $length) {
                        $empty_spots[] = array(
                            'location_id' => $previous_event->LocationID,
                            'day'         => $previous_event_day,
                            'time'        => $previous_event_time,
                            'gap'         => gmdate('G:i', $empty_space),
                            'unix_time'   => $unix_time
                        );
                    }
                } else {
                    $previous_time = $previous_event_time;
                }

                $unix_time = strtotime($event_day.' '.$previous_time);
                $empty_space = strtotime($event_start_date) - $unix_time;
                if ($empty_space >= $length) {
                    $empty_spots[] = array(
                        'location_id' => $event->LocationID,
                        'day'         => $event_day,
                        'time'        => $previous_time,
                        'gap'         => gmdate('G:i', $empty_space),
                        'unix_time'   => $unix_time
                    );
                }

                $previous_event      = $event;
                $previous_end_date   = $previous_event->getEndDate();
                $previous_event_day  = date('Y-m-d',strtotime($previous_end_date));
                $previous_event_time = date('H:i',strtotime($previous_end_date));
            }

            // check the empty space between the last event on the list and the top limit
            $end_limit = $previous_event_day.' '.$end_time;
            $unix_time = strtotime($previous_end_date);
            $empty_space = strtotime($end_limit) - $unix_time;
            if ($empty_space >= $length) {
                $empty_spots[] = array(
                    'location_id' => $previous_event->LocationID,
                    'day'         => $previous_event_day,
                    'time'        => $previous_event_time,
                    'gap'         => gmdate('G:i', $empty_space),
                    'unix_time'   => $unix_time
                );
            }
        }

        // now add the days/venues without any events, ie completely free
        $full_gap = strtotime($end_time) - strtotime($start_time);
        foreach ($days as $day) {
            foreach ($locations as $loc) {
                if (!array_key_exists($day,$control) || !array_key_exists($loc,$control[$day])) {
                    // we add this location and day
                    $empty_spots[] = array(
                        'location_id' => $loc,
                        'day'         => $day,
                        'time'        => $start_time,
                        'gap'         => gmdate('G:i', $full_gap),
                        'unix_time'   => strtotime($day.' '.$start_time)
                    );
                }
            }
        }

        // sort by location, day, time
        usort($empty_spots, function ($a, $b) {
            if ($a['location_id'] == $b['location_id']) {
                if ($a['unix_time'] == $b['unix_time']) {
                    return 0;
                }
                return $a['unix_time'] < $b['unix_time'] ? -1 : 1;
            }
            return $a['location_id'] < $b['location_id'] ? -1 : 1;
        });


        return $this->ok(array('empty_spots' => $empty_spots));
    }

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

    /**
     * @return SS_HTTPResponse
     */
    public function shareEmail(){
        try {


            $data = $this->getJsonRequest();

            if (!$data) return $this->serverError();

            $this->schedule_manager->sendEmail($data);

        }
        catch(EntityValidationException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }
}