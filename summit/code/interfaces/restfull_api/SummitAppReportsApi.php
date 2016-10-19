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
class SummitAppReportsApi extends AbstractRestfulJsonApi {


    /**
     * @var ISummitAssistanceRepository
     */
    private $assistance_repository;

    /**
     * @var IEntityRepository
     */
    private $summit_repository;

    /**
     * @var IEntityRepository
     */
    private $report_repository;

    /**
     * @var IEntityRepository
     */
    private $rsvp_repository;

    /**
     * @var IEntityRepository
     */
    private $event_repository;

    /**
     * @var IEntityRepository
     */
    private $room_metrics_repository;

    /**
     * @var IEntityRepository
     */
    private $category_repository;

    /**
     * @var IEntityRepository
     */
    private $presentation_repository;

    /**
     * @var ISummitService
     */
    private $summit_service;

    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitAssistanceRepository $assistance_repository,
        ISummitReportRepository $report_repository,
        IRSVPRepository $rsvp_repository,
        ISummitEventRepository $event_repository,
        IRoomMetricsRepository $room_metrics_repository,
        IPresentationCategoryRepository $category_repository,
        ISummitPresentationRepository $presentation_repository,
        ISummitService $summit_service
    )
    {
        parent::__construct();
        $this->assistance_repository         = $assistance_repository;
        $this->summit_repository             = $summit_repository;
        $this->report_repository             = $report_repository;
        $this->rsvp_repository               = $rsvp_repository;
        $this->event_repository              = $event_repository;
        $this->room_metrics_repository       = $room_metrics_repository;
        $this->category_repository           = $category_repository;
        $this->presentation_repository       = $presentation_repository;
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
        return true;
    }

    protected function authenticate() {
        return true;
    }

    static $url_handlers = array
    (
        'PUT $REPORT!'                      => 'updateReport',
        'GET export'                        => 'handleExport',
        'GET speaker_report'                => 'getSpeakerReport',
        'GET room_report'                   => 'getRoomReport',
        'GET room_metrics/$EVENT_ID'        => 'getRoomMetrics',
        'GET presentation_report'           => 'getPresentationReport',
        'GET video_report'                  => 'getVideoReport',
        'GET rsvp_report'                   => 'getRsvpReport',
        'GET track_questions_report'        => 'getTrackQuestionsReport',
        'GET presentations_company_report'  => 'getPresentationsCompanyReport',
    );

    static $allowed_actions = array(
        'getSpeakerReport',
        'getRoomReport',
        'getPresentationReport',
        'getVideoReport',
        'handleExport',
        'updateReport',
        'getRsvpReport',
        'getRoomMetrics',
        'getTrackQuestionsReport',
        'getPresentationsCompanyReport',
    );

    public function handleExport(SS_HTTPRequest $request)
    {
        $api = SummitAppReportsExportApi::create();
        return $api->handleRequest($request, DataModel::inst());
    }

    public function getSpeakerReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : '';
            $page_size    = (isset($query_string['items'])) ? Convert::raw2sql($query_string['items']) : '';
            $sort         = (isset($query_string['sort'])) ? Convert::raw2sql($query_string['sort']) : 'ID';
            $sort_dir     = (isset($query_string['sort_dir'])) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC';
            $filter       = (isset($query_string['filter'])) ? $query_string['filter'] : 'all';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $assistances = $this->assistance_repository->getAssistanceBySummit($summit_id, $page, $page_size, $sort, $sort_dir, $filter);

            $assistance_array = array();

            foreach($assistances['Data'] as $assistance) {
                $assistance_array[] = array(
                    'speaker_id'    => $assistance['speaker_id'],
                    'member_id'     => $assistance['member_id'],
                    'name'          => $assistance['name'],
                    'email'         => $assistance['email'],
                    'phone'         => $assistance['phone'],
                    'company'       => $assistance['company'],
                    'presentation'  => $assistance['presentation'],
                    'track'         => $assistance['track'],
                    'confirmed'     => intVal($assistance['confirmed']),
                    'registered'    => intVal($assistance['registered']),
                    'checked_in'    => intVal($assistance['checked_in'])
                );
            }

            return $this->ok(array('total' => $assistances['Total'], 'data' => $assistance_array));
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

    public function updateReport(SS_HTTPRequest $request)
    {
        try
        {
            if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
            $summit_id       = intval($request->param('SUMMIT_ID'));
            $report          = $request->param('REPORT');
            $report_data     = $this->getJsonRequest();

            $summit = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            switch ($report) {
                case 'speaker_report':
                case 'presentation_report':
                    $this->summit_service->updateAssistance($summit, $report_data);
                    break;
                case 'room_report':
                    $this->summit_service->updateHeadCount($summit, $report_data);
                    break;
                case 'video_report':
                    $this->summit_service->updateVideoDisplay($summit, $report_data['report_data']);
                    $this->summit_service->updateReportConfig($report,$report_data['report_config']);
                    break;
            }
            return $this->ok();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(ValidationException $ex2)
        {
            SS_Log::log($ex2->getResult()->messageList(), SS_Log::WARN);
            return $this->validationError($ex2->getResult()->messageList());
        }
        catch(NotFoundEntityException $ex3)
        {
            SS_Log::log($ex3->getMessage(), SS_Log::WARN);
            return $this->notFound($ex3->getMessages());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getRoomReport(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            $query_string = $request->getVars();
            $event_type   = (isset($query_string['event_type'])) ? Convert::raw2sql($query_string['event_type']) : 'all';
            $venues       = (isset($query_string['venues'])) ? $query_string['venues'] : '';

            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $days = $summit->getDates();

            $report_array = array();

            foreach($days as $day) {
                $day_report = $this->assistance_repository->getRoomsBySummitAndDay($summit_id,$day->Date,$event_type,$venues);
                $report_array[$day->Label] = array();
                foreach ($day_report as $rooms) {

                    $report_array[$day->Label][] = array(
                        'id'         => intVal($rooms['id']),
                        'date'       => $summit->convertDateFromUTC2TimeZone($rooms['start_date'],'m/d/Y'),
                        'start_time' => $summit->convertDateFromUTC2TimeZone($rooms['start_date'],'g:ia'),
                        'end_time'   => $summit->convertDateFromUTC2TimeZone($rooms['end_date'],'g:ia'),
                        'code'       => empty($rooms['code'])? 'TBD': $rooms['code'],
                        'title'      => $rooms['event'],
                        'room'       => $rooms['room'],
                        'venue'      => $rooms['venue'],
                        'capacity'   => $rooms['capacity'],
                        'speakers'   => intVal($rooms['speakers']),
                        'headcount'  => intVal($rooms['headcount']),
                        'total'      => intVal($rooms['total'])
                    );
                }
            }

            $calendar_count = $this->assistance_repository->getAttendeesWithCalendar($summit_id);
            $return_array['calendar_count'] = $calendar_count->value();
            $return_array['report'] = $report_array;

            return $this->ok($return_array);
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

    public function getVideoReport(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            $query_string = $request->getVars();
            $report       = $this->report_repository->getByName('video_report');
            $tracks       = '';
            if(isset($query_string['tracks']) && $query_string['tracks'] != '') {
                $tracks = html_entity_decode($query_string['tracks']);
            } else if ($report) {
                $tracks = $report->getConfigByName('Tracks');;
            }

            $venues = (isset($query_string['venues'])) ? $query_string['venues'] : '';
            $start_date = (isset($query_string['start_date']) && $query_string['start_date']) ? date('Y-m-d',strtotime($query_string['start_date'])) : '';
            $end_date = (isset($query_string['end_date']) && $query_string['end_date']) ? date('Y-m-d',strtotime($query_string['end_date'])) : '';
            $search_term = (isset($query_string['search_term'])) ? $query_string['search_term'] : '';

            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $days = $summit->getDates();

            $report_array = array();

            foreach($days as $day) {
                $day_report = $this->presentation_repository->getPresentationMaterialBySummitAndDay($summit_id,$day->Date,$tracks,$venues,$start_date,$end_date,$search_term);
                $report_array[$day->Label] = array();
                foreach ($day_report as $videos) {

                    $report_array[$day->Label][] = array(
                        'id'          => intVal($videos['id']),
                        'date'        => $summit->convertDateFromUTC2TimeZone($videos['start_date'],'m/d/Y'),
                        'start_time'  => $summit->convertDateFromUTC2TimeZone($videos['start_date'],'g:ia'),
                        'end_time'    => $summit->convertDateFromUTC2TimeZone($videos['end_date'],'g:ia'),
                        'tags'        => $videos['tags'].','.$videos['speakers'].',OpenStack Summit Austin',
                        'title'       => $videos['event'],
                        'description' => $videos['description'],
                        'room'        => $videos['room'],
                        'venue'       => $videos['venue'],
                        'display'     => intval($videos['display']),
                        'youtube'     => $videos['youtube_id']
                    );
                }
            }

            $return_array['tracks'] = ($tracks) ? explode(',',$tracks) : '';
            $return_array['report'] = $report_array;

            return $this->ok($return_array);
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

    public function getPresentationReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : '';
            $page_size    = (isset($query_string['items'])) ? Convert::raw2sql($query_string['items']) : '';
            $sort         = (isset($query_string['sort'])) ? Convert::raw2sql($query_string['sort']) : 'presentation';
            $sort_dir     = (isset($query_string['sort_dir'])) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC';
            $search_term  = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
            $filter       = (isset($query_string['filter'])) ? $query_string['filter'] : 'all';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $presentations = $this->presentation_repository->getPresentationsAndSpeakersBySummit($summit_id,$page,$page_size,$sort,$sort_dir,$search_term,$filter);

            $presentation_array = array();

            foreach($presentations['Data'] as $presentation) {

                $presentation_array[] = array(
                    'presentation_id' => $presentation['P_ID'],
                    'assistance_id'   => $presentation['Assistance_id'],
                    'title'           => $presentation['Presentation'],
                    'published'       => $presentation['Published'],
                    'track'           => $presentation['Track'],
                    'start_date'      => $summit->convertDateFromUTC2TimeZone($presentation['Start_Date'],'m/d/Y g:ia'),
                    'location'        => $presentation['Location'],
                    'speaker_id'      => $presentation['Speaker_id'],
                    'member_id'       => $presentation['Member_id'],
                    'name'            => $presentation['Name'],
                    'email'           => $presentation['Email'],
                    'phone'           => $presentation['Phone'],
                    'code_type'       => $presentation['Type'],
                    'promo_code'      => $presentation['Code'],
                    'confirmed'       => intVal($presentation['Confirmed']),
                    'registered'      => intVal($presentation['Registered']),
                    'checked_in'      => intVal($presentation['Checked_in'])
                );
            }

           return $this->ok(array('total' => $presentations['Total'], 'data' => $presentation_array));
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

    public function getRsvpReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : '';
            $page_size    = (isset($query_string['items'])) ? Convert::raw2sql($query_string['items']) : '';
            $search_term  = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $events  = $this->event_repository->searchBySummitTermAndHasRSVP($summit,$search_term);

            $results = array('event_count' => 0, 'data' => array(), 'headers' => array());
            if (count($events)) {
                if (count($events) == 1) {
                    $results['event_count'] = 1;
                    $event = array_pop($events);
                    list($rsvps,$total) = $this->rsvp_repository->getByEventPaged($event->ID,$page,$page_size);
                    $rsvp_array_template = array();
                    foreach ($event->RSVPTemplate()->Questions()->sort('Order') as $question) {
                        if ($question->Label) {
                            $rsvp_array_template[$question->Label] = '';
                            $results['headers'][] = $question->Label;
                        }
                    }

                    if (count($rsvps)) {
                        foreach($rsvps as $rsvp) {
                            $rsvp_array = $rsvp_array_template;
                            foreach ($rsvp->Answers() as $answer) {
                                $rsvp_array[$answer->Question()->Label] = $answer->getFormattedAnswer();
                            }

                            $results['data'][] = $rsvp_array;
                        }
                    }
                    $results['event'] = array(
                        'event_id' => intval($event->ID),
                        'title'    => $event->getTitle(),
                        'date'     => $event->getDateNice(),
                    );
                    $results['total'] = $total;
                } else {
                    $results['event_count'] = count($events);
                    foreach($events as $event) {
                        $results['data'][] = array(
                            'event_id' => intval($event->ID),
                            'title'    => $event->getTitle(),
                            'date'     => $event->getDateNice(),
                        );
                    }
                }
            }

            return $this->ok($results);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $ex->getMessage();
        }
    }

    public function getTrackQuestionsReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : '';
            $page_size    = (isset($query_string['items'])) ? Convert::raw2sql($query_string['items']) : '';
            $search_term  = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $tracks  = $this->category_repository->searchBySummitAndHasExtraQuestions($summit_id,$search_term);

            $results = array('track_count' => 0, 'data' => array(), 'headers' => array('ID','Presentation'));
            if (count($tracks)) {
                if (count($tracks) == 1) {
                    $results['track_count'] = 1;
                    $track = array_pop($tracks);
                    list($presentations,$total) = $this->presentation_repository->getByCategoryPaged($track->ID,$page,$page_size);
                    $presentation_array_template = array('ID' => 0, 'Presentation' => '');

                    foreach ($track->ExtraQuestions() as $question) {
                        if ($question->Label) {
                            $presentation_array_template[$question->Label] = '';
                            $results['headers'][] = $question->Label;
                        }
                    }

                    if (count($presentations)) {
                        foreach($presentations as $presentation) {
                            $presentation_array = $presentation_array_template;
                            $presentation_array['ID'] = $presentation->ID;
                            $presentation_array['Presentation'] = $presentation->Title;

                            foreach ($presentation->ExtraAnswers() as $answer) {
                                $presentation_array[$answer->Question()->Label] = $answer->getFormattedAnswer();
                            }

                            $results['data'][] = $presentation_array;
                        }
                    }
                    $results['track'] = array(
                        'track_id' => intval($track->ID),
                        'title'    => $track->getTitle(),
                    );
                    $results['total'] = $total;
                } else {
                    $results['track_count'] = count($tracks);
                    foreach($tracks as $track) {
                        $results['data'][] = array(
                            'track_id' => intval($track->ID),
                            'title'    => $track->getTitle(),
                        );
                    }
                }
            }

            return $this->ok($results);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $ex->getMessage();
        }
    }

    public function getPresentationsCompanyReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : '';
            $page_size    = (isset($query_string['items'])) ? Convert::raw2sql($query_string['items']) : '';
            $sort         = (isset($query_string['sort'])) ? Convert::raw2sql($query_string['sort']) : 'presentation';
            $sort_dir     = (isset($query_string['sort_dir'])) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC';
            $search_term  = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $presentations = $this->presentation_repository->searchByCompanyPaged($summit_id,$page,$page_size,$sort,$sort_dir,$search_term);

            return $this->ok(array('total' => $presentations['Total'], 'data' => $presentations['Data']));
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

    public function getRoomMetrics(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            $event_id     = intval($request->param('EVENT_ID'));
            $event        = $this->event_repository->getById($event_id);
            $time_offset  = $request->getVar('offset');

            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            if(is_null($event)) throw new NotFoundEntityException('Event', sprintf(' id %s', $event_id));

            $time_offset = $summit->convertDateFromTimeZone2UTC($event->getBeginDateYMD().' '.$time_offset);

            $metrics = $this->room_metrics_repository->getByRoomAndDate($event->LocationID, $event->getStartDateUTC(), $event->getEndDateUTC(), $time_offset)->limit(10);
            $metrics_array = array();

            foreach ($metrics as $metric) {
                $type = $metric->Type()->Type;
                $unit = $metric->Type()->Unit;
                $time = $summit->convertDateFromUTC2TimeZone(date('H:i:s',$metric->TimeStamp),'g:iA');
                $data = array($time, $metric->Value);

                if (!isset($metrics_array[$type]))
                    $metrics_array[$type] = array('type' => $type, 'unit' => $unit, 'metrics' => array());
                $metrics_array[$type]['metrics'][] = $data;
            }

            return $this->ok($metrics_array);
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