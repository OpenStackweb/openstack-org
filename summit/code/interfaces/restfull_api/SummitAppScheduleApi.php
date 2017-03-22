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
use Eluceo\iCal\Component\Calendar;
use Openstack\Annotations as CustomAnnotation;
/**
 * Class SummitAppScheduleApi
 */
final class SummitAppScheduleApi extends AbstractRestfulJsonApi
{

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

    /**
     * @var IViewModelMapper
     */
    private $schedule_view_model_mapper;

    /**
     * @var IViewModelMapper
     */
    private $schedule_search_view_model_mapper;

    /**
     * @var IViewModelMapper
     */
    private $full_schedule_view_model_mapper;

    /**
     * SummitAppScheduleApi constructor.
     * @param ISummitRepository $summit_repository
     * @param ISummitEventRepository $summitevent_repository
     * @param ISummitPresentationRepository $summitpresentation_repository
     * @param IEventFeedbackRepository $eventfeedback_repository
     * @param ISummitAttendeeRepository $attendee_repository
     * @param IScheduleManager $schedule_manager
     * @param IViewModelMapper $schedule_view_model_mapper
     * @param IViewModelMapper $schedule_search_view_model_mapper
     * @param IViewModelMapper $full_schedule_view_model_mapper
     */
    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitEventRepository $summitevent_repository,
        ISummitPresentationRepository $summitpresentation_repository,
        IEventFeedbackRepository $eventfeedback_repository,
        ISummitAttendeeRepository $attendee_repository,
        IScheduleManager $schedule_manager,
        IViewModelMapper $schedule_view_model_mapper,
        IViewModelMapper $schedule_search_view_model_mapper,
        IViewModelMapper $full_schedule_view_model_mapper
    )
    {
        parent::__construct();
        $this->securityToken                     = new SecurityToken();
        $this->summit_repository                 = $summit_repository;
        $this->summitevent_repository            = $summitevent_repository;
        $this->summitpresentation_repository     = $summitpresentation_repository;
        $this->eventfeedback_repository          = $eventfeedback_repository;
        $this->attendee_repository               = $attendee_repository;
        $this->schedule_manager                  = $schedule_manager;
        $this->schedule_view_model_mapper        = $schedule_view_model_mapper;
        $this->schedule_search_view_model_mapper = $schedule_search_view_model_mapper;
        $this->full_schedule_view_model_mapper   = $full_schedule_view_model_mapper;
    }

    protected function isApiCall()
    {
        $request = $this->getRequest();
        if (is_null($request)) return false;
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        $request = $this->getRequest();
        if (!strstr(strtolower($request->getURL()), "schedule/export/ics") === false) return true;
        return $this->checkOwnAjaxRequest();
    }

    protected function authenticate()
    {
        return true;
    }

    static $url_handlers = [
        'GET level'                               => 'getScheduleByLevel',
        'GET track'                               => 'getScheduleByTrack',
        'GET search'                              => 'getSearchResults',
        'GET export/ics'                          => 'ExportEventToICS',
        'GET empty_spots'                         => 'getEmptySpots',
        'GET full'                                => 'getFullSchedule',
        'GET '                                    => 'getScheduleByDay',
        'PUT $EventID!/favorite'                  => 'AddToFavorites',
        'DELETE $EventID!/favorite'               => 'RemoveFromFavorites',
        'DELETE $EventID!/rsvp'                   => 'deleteRSVP',
        'PUT $EventID!/rsvp/$RsvpID!'             => 'updateRSVP',
        'PUT $EventID!/synch/google/$CalEventID!' => 'synchEvent',
        'PUT $EventID!'                           => 'addToSchedule',
        'DELETE $EventID!/synch/google'           => 'unSynchEvent',
        'DELETE $EventID!'                        => 'removeFromSchedule',
        'POST $EventID!/feedback'                 => 'addFeedback',
        'POST $EventID!/share'                    => 'shareEmail',
        'POST $EventID!/rsvp'                     => 'rsvpEvent',
    ];

    static $allowed_actions = [
        'getScheduleByDay',
        'getScheduleByLevel',
        'getScheduleByTrack',
        'getSearchResults',
        'getEmptySpots',
        'addToSchedule',
        'removeFromSchedule',
        'addFeedback',
        'getFullSchedule',
        'shareEmail',
        'synchEvent',
        'unSynchEvent',
        'rsvpEvent',
        'updateRSVP',
        'deleteRSVP',
        'ExportEventToICS',
        'AddToFavorites',
        'RemoveFromFavorites',
    ];

    /**
     * @CustomAnnotation\CachedMethod(lifetime=900, format="JSON", conditions={@CustomAnnotation\CacheMethodConditionMemberNotLogged()})
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getScheduleByTrack(SS_HTTPRequest $request)
    {
        $query_string = $request->getVars();
        $summit_id    = intval($request->param('SUMMIT_ID'));
        $track        = isset($query_string['track']) ? Convert::raw2sql($query_string['track']) : null;
        $summit       = null;

        if (intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));
        if (strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if (is_null($summit))
            return $this->notFound('summit not found!');

        $schedule = $summit->getScheduleByTrack($track);
        $events   = $this->schedule_view_model_mapper->map([$schedule->toArray(), $summit]);

        return $this->ok([
            'track'  => $track,
            'events' => $events
        ]);
    }

    /**
     * @CustomAnnotation\CachedMethod(lifetime=900, format="JSON", conditions={@CustomAnnotation\CacheMethodConditionMemberNotLogged()})
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getScheduleByLevel(SS_HTTPRequest $request)
    {
        $query_string = $request->getVars();
        $summit_id    = intval($request->param('SUMMIT_ID'));
        $level        = isset($query_string['level']) ? Convert::raw2sql($query_string['level']) : null;
        $summit       = null;

        if (intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));
        if (strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if (is_null($summit))
            return $this->notFound('summit not found!');

        $schedule = $summit->getScheduleByLevel($level);
        $events   = $this->schedule_view_model_mapper->map([$schedule->toArray(), $summit]);

        return $this->ok( [
            'level'  => $level,
            'events' => $events
        ]);
    }

    /**
     * @param SS_HTTPRequest $request
     * @return string
     */
    protected function getCacheKey(SS_HTTPRequest $request)
    {
        $key = parent::getCacheKey($request);

        if(Member::currentUserID()){
            $member    = Member::currentUser();
            $summit_id = intval($request->param('SUMMIT_ID'));
            $attendee  = $member->getSummitAttendee($summit_id);
            if($attendee) {
                $schedule_events = $attendee->getScheduleEventIds($summit_id);
                $key .= '-sched-' . implode('.', $schedule_events);
            }

            $favorites_events = $member->getFavoritesEventIds($summit_id);
            $key .= '-fav-' . implode('.', $favorites_events);
        }
        return $key;
    }

    /**
     * @CustomAnnotation\CachedMethod(lifetime=900, format="JSON")
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getScheduleByDay(SS_HTTPRequest $request)
    {
        $query_string    = $request->getVars();
        $summit_id       = intval($request->param('SUMMIT_ID'));
        $day             = isset($query_string['day']) ? Convert::raw2sql($query_string['day']) : null;
        $location        = isset($query_string['location']) ? intval(Convert::raw2sql($query_string['location'])) : null;
        $inc_blackouts   = isset($query_string['blackouts']) ? intval(Convert::raw2sql($query_string['blackouts'])) : null;
        $summit          = null;

        if (intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));

        if (strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if (is_null($summit))
            return $this->notFound('summit not found!');

        $schedule  = $summit->getSchedule($day, $location);
        $blackouts = ($inc_blackouts) ? $summit->getBlackouts($day, $location) : new ArrayList();

        $schedule->merge($blackouts);

        $events = $this->schedule_view_model_mapper->map([$schedule->toArray(), $summit]);

        return $this->ok([
            'day'    => $day,
            'events' => $events
        ]);
    }

    public function getSearchResults(SS_HTTPRequest $request)
    {

        $query_string = $request->getVars();
        $summit_id    = intval($request->param('SUMMIT_ID'));
        $term         = isset($query_string['term']) ? Convert::raw2sql($query_string['term']) : null;
        $summit       = null;

        if (intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));

        if (strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if (is_null($summit))
            return $this->notFound('summit not found!');

        $search_results = $this->summitevent_repository->searchBySummitAndTerm($summit, $term);
        $events         = $this->schedule_search_view_model_mapper->map([$search_results, $summit]);

        return $this->ok(['events' => $events]);
    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getEmptySpots(SS_HTTPRequest $request)
    {

        $query_string = $request->getVars();
        $summit_id    = intval($request->param('SUMMIT_ID'));
        $days         = isset($query_string['days']) ? json_decode($query_string['days']) : null;
        $start_time   = isset($query_string['start_time']) ? $query_string['start_time'] : '07:00:00';
        $end_time     = isset($query_string['end_time']) ? $query_string['end_time'] : '22:00:00';
        $locations    = isset($query_string['locations']) ? json_decode($query_string['locations']) : null;
        $length       = isset($query_string['length']) ? intval($query_string['length']) : 0;
        $summit       = null;

        if (intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));
        if (strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if (is_null($summit))
            return $this->notFound('summit not found!');

        if (empty($days) || empty($locations) || empty($length))
            return $this->validationError('Parameters missing.');

        $events         = $this->summitevent_repository->getPublishedByTimeAndVenue($summit, $days, $start_time, $end_time, $locations);
        $empty_spots    = [];
        $control        = [];
        $previous_event = $previous_end_date = $previous_event_day = $previous_event_time = null;

        if ($events) {
            foreach ($events as $event) {
                $event_start_date = $event->getStartDate();
                $event_day        = date('Y-m-d', strtotime($event_start_date));

                $control[$event_day][$event->LocationID] = true; //control array to see what days and locations had no events at all

                // if first event or first on this venue or first of the day we use the lower limit
                if ($previous_event == null) {
                    $previous_time = $start_time;
                } else if ($previous_event->LocationID != $event->LocationID || $previous_event_day != $event_day) {
                    $previous_time = $start_time;

                    // if it is the first event of the room/day, add empty spot between last event and top limit
                    $end_limit = $previous_event_day . ' ' . $end_time;
                    $unix_time = strtotime($previous_end_date);
                    $empty_space = strtotime($end_limit) - $unix_time;
                    if ($empty_space >= $length) {
                        $empty_spots[] = array(
                            'location_id' => $previous_event->LocationID,
                            'day' => $previous_event_day,
                            'time' => $previous_event_time,
                            'gap' => gmdate('G:i', $empty_space),
                            'unix_time' => $unix_time
                        );
                    }
                } else {
                    $previous_time = $previous_event_time;
                }

                $unix_time = strtotime($event_day . ' ' . $previous_time);
                $empty_space = strtotime($event_start_date) - $unix_time;
                if ($empty_space >= $length) {
                    $empty_spots[] = array(
                        'location_id' => $event->LocationID,
                        'day' => $event_day,
                        'time' => $previous_time,
                        'gap' => gmdate('G:i', $empty_space),
                        'unix_time' => $unix_time
                    );
                }

                $previous_event = $event;
                $previous_end_date = $previous_event->getEndDate();
                $previous_event_day = date('Y-m-d', strtotime($previous_end_date));
                $previous_event_time = date('H:i', strtotime($previous_end_date));
            }

            // check the empty space between the last event on the list and the top limit
            $end_limit = $previous_event_day . ' ' . $end_time;
            $unix_time = strtotime($previous_end_date);
            $empty_space = strtotime($end_limit) - $unix_time;
            if ($empty_space >= $length) {
                $empty_spots[] = array(
                    'location_id' => $previous_event->LocationID,
                    'day' => $previous_event_day,
                    'time' => $previous_event_time,
                    'gap' => gmdate('G:i', $empty_space),
                    'unix_time' => $unix_time
                );
            }
        }

        // now add the days/venues without any events, ie completely free
        $full_gap = strtotime($end_time) - strtotime($start_time);
        foreach ($days as $day) {
            foreach ($locations as $loc) {
                if (!array_key_exists($day, $control) || !array_key_exists($loc, $control[$day])) {
                    // we add this location and day
                    $empty_spots[] = array(
                        'location_id' => $loc,
                        'day' => $day,
                        'time' => $start_time,
                        'gap' => gmdate('G:i', $full_gap),
                        'unix_time' => strtotime($day . ' ' . $start_time)
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

    public function addToSchedule()
    {
        try {
            $summit_id  = (int)$this->request->param('SUMMIT_ID');
            $event_id   = (int)$this->request->param('EventID');
            $member     = Member::currentUser();

            if (is_null($member)) return $this->permissionFailure();

            if (intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if (strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if (is_null($summit))
                return $this->notFound('summit not found!');

            $this->schedule_manager->addEventToSchedule(Member::currentUserID(), $event_id);

            return $this->ok();
        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function removeFromSchedule()
    {
        try {
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $event_id = (int)$this->request->param('EventID');
            $member = Member::currentUser();

            if (is_null($member)) return $this->permissionFailure();

            if (intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if (strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if (is_null($summit))
                return $this->notFound('summit not found!');

            $this->schedule_manager->removeEventFromSchedule(Member::currentUserID(), $event_id);
            return $this->ok();
        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @return SS_HTTPResponse
     */
    public function addFeedback()
    {
        try {
            $data = $this->getJsonRequest();
            $event_id = (int)$this->request->param('EventID');
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $member_id = Member::CurrentUserID();

            if (!$data) return $this->serverError();

            if (intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if (strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if (is_null($summit))
                return $this->notFound('summit not found!');

            if (intval($event_id) > 0)
                $event = $this->summitevent_repository->getById(intval($event_id));

            if (is_null($event))
                return $this->notFound('event not found!');

            if(!ScheduleManager::allowToSee($event))
                return $this->notFound('event not found!');

            else if ($event->getSummit()->getIdentifier() != intval($summit_id))
                return $this->notFound('event not found in current summit');

            $data['summit_id'] = $summit->ID;
            $data['event_id']  = $event_id;
            $data['member_id'] = $member_id;

            return $this->created($this->schedule_manager->addFeedback($data));

        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    const MaxEventShareByEmailPerSession = 5;

    /**
     * @return SS_HTTPResponse
     */
    public function shareEmail(SS_HTTPRequest $request)
    {

        try {
            $event_id = intval($request->param('EventID'));
            $event    = $this->summitevent_repository->getById($event_id);

            if (is_null($event))
                throw new NotFoundEntityException('SummitEvent', sprintf(' id %s', $event_id));

           if(!ScheduleManager::allowToSee($event))
               throw new NotFoundEntityException('SummitEvent', sprintf(' id %s', $event_id));

            $data = $this->getJsonRequest();
            if (!$data) return $this->serverError();

            if (!$data['from'] || !$data['to']) {
                throw new EntityValidationException('Please enter From and To email addresses.');
            }

            if (filter_var($data['from'], FILTER_VALIDATE_EMAIL) === false)
                throw new EntityValidationException('from is invalid email address');

            if (filter_var($data['to'], FILTER_VALIDATE_EMAIL) === false)
                throw new EntityValidationException('to is invalid email address');

            if (!$data['token'])
                throw new EntityValidationException('session lost');

            $token       = Session::get(SummitAppSchedPage_Controller::EventShareByEmailTokenKey);
            $token_count = Session::get(SummitAppSchedPage_Controller::EventShareByEmailCountKey);


            if ($data['token'] != $token) {
                throw new EntityValidationException('session lost');
            }

            if ($token_count > self::MaxEventShareByEmailPerSession) {
                throw new EntityValidationException('you reach your limit of emails per event per session');
            }

            $subject = 'Fwd: ' . $event->Title;
            $body    = $event->Title . '<br>' . $event->Abstract . '<br><br>Check it out: ' . $event->getLink();

            $email = EmailFactory::getInstance()->buildEmail($data['from'], $data['to'], $subject, $body);
            $email->send();
            ++$token_count;
            Session::set(SummitAppSchedPage_Controller::EventShareByEmailCountKey, $token_count);
            return $this->ok();

        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @CustomAnnotation\CachedMethod(lifetime=900, format="JSON", conditions={@CustomAnnotation\CacheMethodConditionMemberNotLogged()})
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getFullSchedule(SS_HTTPRequest $request)
    {
        $query_string = $request->getVars();
        $summit_id    = intval($request->param('SUMMIT_ID'));
        $sort         = isset($query_string['sort']) ? Convert::raw2sql($query_string['sort']) : null;
        $summit       = null;

        if (intval($summit_id) > 0)
            $summit = $this->summit_repository->getById(intval($summit_id));

        if (strtolower($summit_id) === 'current')
            $summit = Summit::ActiveSummit();

        if (is_null($summit))
            return $this->notFound('summit not found!');

        $schedule  = $summit->getSchedule();
        $events    = $this->full_schedule_view_model_mapper->map([$schedule, $sort]);

        return $this->ok($events);
    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function synchEvent(SS_HTTPRequest $request)
    {
        try {
            $summit_id    = (int)$request->param('SUMMIT_ID');
            $event_id     = (int)$request->param('EventID');
            $cal_event_id = $request->param('CalEventID');
            $member       = Member::currentUser();

            if (is_null($member)) return $this->permissionFailure();

            if (intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if (strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if (is_null($summit))
                return $this->notFound('summit not found!');

            $this->schedule_manager->saveSynchId(Member::currentUserID(), $event_id, 'google', $cal_event_id);

            return $this->ok($cal_event_id);

        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function unSynchEvent(SS_HTTPRequest $request)
    {
        try {
            $summit_id = (int)$request->param('SUMMIT_ID');
            $event_id  = (int)$request->param('EventID');
            $member    = Member::currentUser();

            if (is_null($member)) return $this->permissionFailure();

            if (intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));
            if (strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if (is_null($summit))
                return $this->notFound('summit not found!');

            $this->schedule_manager->saveSynchId(Member::currentUserID(), $event_id, 'google', '');

            return $this->ok();
        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @return SS_HTTPResponse
     */
    public function rsvpEvent()
    {
        try {

            $data      = $this->getJsonRequest();
            $event_id  = (int)$this->request->param('EventID');
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $member_id = Member::CurrentUserID();

            if ($member_id <= 0)
                return $this->forbiddenError();

            if (!$data) return $this->serverError();

            if (intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if (strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if (is_null($summit))
                return $this->notFound('summit not found!');

            if (intval($event_id) > 0)
                $event = $this->summitevent_repository->getById(intval($event_id));

            if (is_null($event))
                return $this->notFound('event not found!');

            else if ($event->getSummit()->getIdentifier() != intval($summit_id))
                return $this->notFound('event not found in current summit');

            $data['summit_id'] = $summit->ID;
            $data['event_id']  = $event_id;
            $data['member_id'] = $member_id;

            $rsvp = $this->schedule_manager->addRSVP($data, new SummitAttendeeRSVPEmailSender);

            return $this->created($rsvp->ID);

        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @return SS_HTTPResponse
     */
    public function updateRSVP()
    {
        try {

            $data      = $this->getJsonRequest();
            $event_id  = (int)$this->request->param('EventID');
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $rsvp_id   = (int)$this->request->param('RsvpID');
            $member_id = Member::CurrentUserID();

            if ($member_id <= 0)
                return $this->forbiddenError();

            if (!$data) return $this->serverError();

            if (intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if (strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if (is_null($summit))
                return $this->notFound('summit not found!');

            if (intval($event_id) > 0)
                $event = $this->summitevent_repository->getById(intval($event_id));

            if (is_null($event))
                return $this->notFound('event not found!');
            else if ($event->getSummit()->getIdentifier() != intval($summit_id))
                return $this->notFound('event not found in current summit');

            $data['summit_id'] = $summit->ID;
            $data['event_id']  = $event_id;
            $data['member_id'] = $member_id;
            $data['rsvp_id']   = $rsvp_id;

            $this->schedule_manager->updateRSVP($data, new SummitAttendeeRSVPEmailSender);

            return $this->updated();

        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @return SS_HTTPResponse
     */
    public function deleteRSVP()
    {
        try {

            $event_id  = (int)$this->request->param('EventID');
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $member_id = Member::CurrentUserID();

            if ($member_id <= 0)
                return $this->forbiddenError();

            if (intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if (strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if (is_null($summit))
                return $this->notFound('summit not found!');

            if (intval($event_id) > 0)
                $event = $this->summitevent_repository->getById(intval($event_id));

            if (is_null($event))
                return $this->notFound('event not found!');

            if ($event->getSummit()->getIdentifier() != intval($summit_id))
                return $this->notFound('event not found in current summit');

            $data['summit_id'] = $summit->ID;
            $data['event_id']  = $event_id;
            $data['member_id'] = $member_id;

            $this->schedule_manager->deleteRSVP($data);

            return $this->deleted();

        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function ExportEventToICS(SS_HTTPRequest $request)
    {
        try {

            $event_ids = $request->getVar('events_id');
            if (is_null($event_ids))
                return $this->validationError("missing events_id param");

            $event_ids = explode(',', $event_ids);
            // https://www.ietf.org/rfc/rfc2445.txt
            $vCalendar = new Calendar('www.openstack.org');
            foreach ($event_ids as $event_id) {
                $event = $this->summitevent_repository->getById($event_id);

                if (is_null($event) || !$event->isPublished() || !ScheduleManager::allowToSee($event))
                    throw new NotFoundEntityException('SummitEvent', sprintf(' id %s', $event_id));

                $vEvent = new \Eluceo\iCal\Component\Event(md5(uniqid(mt_rand(), true)) . "event");
                $vEvent
                    ->setCreated(new \DateTime())
                    ->setDtStart(new \DateTime($event->getStartDateUTC()))
                    ->setDtEnd(new \DateTime($event->getEndDateUTC()))
                    ->setNoTime(false)
                    ->setSummary( $event->Title )
                    ->setDescription(strip_tags($event->Abstract))
                    ->setDescriptionHTML($event->Abstract);

                if($location = $event->getLocation()){
                    $venue = $location;
                    $geo = null;
                    if($location->getTypeName() == SummitVenueRoom::TypeName){
                        $venue = $location->getVenue();
                    }
                    if (is_a($venue,'SummitGeoLocatedLocation')) {
                        $geo = sprintf("%s;%s", $venue->getLat(), $venue->getLng());
                    }
                    $vEvent->setLocation($location->getFullName(), $location->getFullName(), $geo);
                }
                $vCalendar->addComponent($vEvent);
            }
            $response = new SS_HTTPResponse($vCalendar->render(), 200);
            $response->addHeader("Content-type","text/calendar; charset=utf-8");
            $response->addHeader("Content-Disposition","inline; filename=event-" . implode('-',$event_ids) . ".ics");
            return $response;

        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    function AddToFavorites(SS_HTTPRequest $request){
        try {
            $summit_id  = (int)$this->request->param('SUMMIT_ID');
            $event_id   = (int)$this->request->param('EventID');
            $member     = Member::currentUser();

            if (is_null($member)) return $this->permissionFailure();

            if (intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if (strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if (is_null($summit))
                return $this->notFound('summit not found!');

            $this->schedule_manager->addEventToFavorites(Member::currentUserID(), $event_id);

            return $this->ok();
        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    function RemoveFromFavorites(SS_HTTPRequest $request){
        try {
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $event_id = (int)$this->request->param('EventID');
            $member = Member::currentUser();

            if (is_null($member)) return $this->permissionFailure();

            if (intval($summit_id) > 0)
                $summit = $this->summit_repository->getById(intval($summit_id));

            if (strtolower($summit_id) === 'current')
                $summit = Summit::ActiveSummit();

            if (is_null($summit))
                return $this->notFound('summit not found!');

            $this->schedule_manager->removeEventFromFavorites(Member::currentUserID(), $event_id);

            return $this->ok();
        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }
}