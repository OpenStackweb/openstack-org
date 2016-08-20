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
        ISummitService $summit_service
    )
    {
        parent::__construct();
        $this->assistance_repository         = $assistance_repository;
        $this->summit_repository             = $summit_repository;
        $this->report_repository             = $report_repository;
        $this->rsvp_repository               = $rsvp_repository;
        $this->event_repository              = $event_repository;
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
        'PUT $REPORT!'              => 'updateReport',
        'GET speaker_report'        => 'getSpeakerReport',
        'GET room_report'           => 'getRoomReport',
        'GET presentation_report'   => 'getPresentationReport',
        'GET video_report'          => 'getVideoReport',
        'GET rsvp_report'           => 'getRsvpReport',
        'GET export/$REPORT!'       => 'exportReport',
    );

    static $allowed_actions = array(
        'getSpeakerReport',
        'getRoomReport',
        'getPresentationReport',
        'getVideoReport',
        'exportReport',
        'updateReport',
        'getRsvpReport',
    );

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
                $day_report = $this->assistance_repository->getPresentationMaterialBySummitAndDay($summit_id,$day->Date,$tracks,$venues,$start_date,$end_date,$search_term);
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

            $presentations = $this->assistance_repository->getPresentationsAndSpeakersBySummit($summit_id,$page,$page_size,$sort,$sort_dir,$search_term,$filter);

            $presentation_array = array();

            foreach($presentations['Data'] as $presentation) {

                $presentation_array[] = array(
                    'presentation_id' => $presentation['presentation_id'],
                    'assistance_id'   => $presentation['assistance_id'],
                    'title'           => $presentation['presentation'],
                    'published'       => $presentation['published'],
                    'track'           => $presentation['track'],
                    'start_date'      => $summit->convertDateFromUTC2TimeZone($presentation['start_date'],'m/d/Y g:ia'),
                    'location'        => $presentation['location'],
                    'speaker_id'      => $presentation['speaker_id'],
                    'member_id'       => $presentation['member_id'],
                    'name'            => $presentation['name'],
                    'email'           => $presentation['email'],
                    'phone'           => $presentation['phone'],
                    'code_type'       => $presentation['code_type'],
                    'promo_code'      => $presentation['promo_code'],
                    'confirmed'       => intVal($presentation['confirmed']),
                    'registered'      => intVal($presentation['registered']),
                    'checked_in'      => intVal($presentation['checked_in'])
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

            $events = array();
            if ($search_term != '') {
                $events = $this->event_repository->searchBySummitAndTerm($summit,$search_term);
            }

            $results = array('event_count' => 0, 'data' => array());
            if (count($events)) {
                if (count($events) == 1) {
                    $results['event_count'] = 1;
                    $event = array_pop($events);
                    list($rsvps,$total) = $this->rsvp_repository->getByEventPaged($event->ID,$page,$page_size);

                    if (count($rsvps)) {
                        foreach($rsvps as $rsvp) {
                            $other = '';
                            foreach ($rsvp->Answers() as $answer) {
                                $other .= $answer->Question()->Label.': '.$answer->getFormattedAnswer().'<br>';
                            }
                            $results['data'][] = array(
                                'rsvp_id'   => intval($rsvp->ID),
                                'name'      => $rsvp->SubmittedBy()->getFullName(),
                                'email'     => $rsvp->SubmittedBy()->getEmail(),
                                'other'     => $other,
                            );
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

    public function exportReport(SS_HTTPRequest $request) {
        try {

            $query_string = $request->getVars();
            $sort = (isset($query_string['sort'])) ? Convert::raw2sql($query_string['sort']) : 'name';
            $sort_dir = (isset($query_string['sort_dir'])) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC';
            $event_type = (isset($query_string['event_type'])) ? Convert::raw2sql($query_string['event_type']) : 'all';
            $venues = (isset($query_string['venues'])) ? $query_string['venues'] : '';
            $tracks = (isset($query_string['tracks'])) ? html_entity_decode($query_string['tracks']) : 'all';
            $start_date = (isset($query_string['start_date']) && $query_string['start_date']) ? date('Y-m-d',strtotime($query_string['start_date'])) : '';
            $end_date = (isset($query_string['end_date']) && $query_string['end_date']) ? date('Y-m-d',strtotime($query_string['end_date'])) : '';
            $search_term = (isset($query_string['search_term'])) ? $query_string['search_term'] : '';
            $report = $request->param('REPORT');
            $summit_id = intval($request->param('SUMMIT_ID'));
            $summit = $this->summit_repository->getById($summit_id);
            if (is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $ext = 'csv';

            switch ($report) {
                case 'speaker_report' :
                    $filter = (isset($query_string['filter'])) ? $query_string['filter'] : 'all';
                    $report_data = $this->assistance_repository->getAssistanceBySummit($summit_id, null, null, $sort, $sort_dir, $filter);
                    $data = $report_data['Data'];
                    $results = array();
                    foreach ($data as $row) {
                        array_push($results, $row);
                    }
                    $filename = "speaker_report-" . date('Ymd') . "." . $ext;
                    $delimiter = ($ext == 'xls') ? "\t" : ",";
                    return CSVExporter::getInstance()->export($filename, $results, $delimiter);
                    break;
                    break;
                case 'room_report' :
                    $filename = "room_report-" . date('Ymd') . ".xlsx";

                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->getProperties()->setCreator("OpenStack");
                    $objPHPExcel->getProperties()->setTitle("Speaker Per Room Report");
                    $objPHPExcel->setActiveSheetIndex(0);

                    // sheet 1, key codes
                    $categories = $summit->Categories()->toArray();
                    $active_sheet = $objPHPExcel->getActiveSheet();
                    $active_sheet->setTitle("Key Codes");
                    $active_sheet->fromArray(array('Code','Category'), NULL, 'A1');
                    foreach ($categories as $key => $category) {
                        $row = $key + 2;
                        $active_sheet->SetCellValue('A'.$row, $category->Code);
                        $active_sheet->SetCellValue('B'.$row, $category->Title);
                    }

                    // day sheets
                    $days = $summit->getDates();
                    foreach ($days as $day) {
                        $active_sheet = $objPHPExcel->createSheet();
                        $active_sheet->setTitle(date('n-d',strtotime($day->Date)));
                        $active_sheet->fromArray(array('Date','Time','Code','Event','Room','Venue','Capacity','Speakers','Headcount','Total','Speaker Names'), NULL, 'A1');
                        $day_report = $this->assistance_repository->getRoomsBySummitAndDay($summit_id, $day->Date, $event_type, $venues);
                        foreach ($day_report as $key2 => $val) {
                            $row = $key2 + 2;
                            $start_time = $summit->convertDateFromUTC2TimeZone($val['start_date'], 'g:ia');
                            $end_time   = $summit->convertDateFromUTC2TimeZone($val['end_date'], 'g:ia');
                            $date = $summit->convertDateFromUTC2TimeZone($val['start_date'], 'm/d/Y');
                            $time = $start_time . ' - ' . $end_time;
                            unset($val['start_date']);
                            unset($val['end_date']);
                            unset($val['id']);
                            $val['date'] = $date;
                            $val['time'] = $time;
                            $active_sheet->fromArray($val, NULL, 'A'.$row);
                        }
                    }

                    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

                    header('Content-type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment; filename="'.$filename.'"');

                    $objWriter->save('php://output');

                    return;
                    break;
                case 'presentation_report' :
                    $search_term = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
                    $filter = (isset($query_string['filter'])) ? $query_string['filter'] : 'all';
                    $report_data = $this->assistance_repository->getPresentationsAndSpeakersBySummit($summit_id, null, null, $sort, $sort_dir, $search_term,$filter);
                    $data = $report_data['Data'];
                    $results = array();
                    foreach ($data as $row) {
                        $row['start_date'] = $summit->convertDateFromUTC2TimeZone($row['start_date'],'m/d/Y g:ia');
                        unset($row['presentation_id']);
                        unset($row['assistance_id']);
                        array_push($results, $row);
                    }
                    $filename = "presentations_report-" . date('Ymd') . "." . $ext;
                    $delimiter = ($ext == 'xls') ? "\t" : ",";
                    return CSVExporter::getInstance()->export($filename, $results, $delimiter);
                    break;
                case 'video_report' :
                    $filename = "video_report-" . date('Ymd') . ".xlsx";

                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->getProperties()->setCreator("OpenStack");
                    $objPHPExcel->getProperties()->setTitle("Video Output List");

                    // day sheets
                    $days = $summit->getDates();
                    foreach ($days as $day) {
                        $active_sheet = $objPHPExcel->createSheet();
                        $active_sheet->setTitle(date('n-d',strtotime($day->Date)));
                        $active_sheet->fromArray(array('Date','Time','Tags','Event','Description','Room','Venue','Display','YoutubeID'), NULL, 'A1');

                        $day_report = $this->assistance_repository->getPresentationMaterialBySummitAndDay($summit_id,$day->Date,$tracks,$venues,$start_date,$end_date,$search_term);

                        foreach ($day_report as $key2 => $val) {
                            $row = $key2 + 2;
                            $start_time = $summit->convertDateFromUTC2TimeZone($val['start_date'], 'g:ia');
                            $end_time   = $summit->convertDateFromUTC2TimeZone($val['end_date'], 'g:ia');
                            $date = $summit->convertDateFromUTC2TimeZone($val['start_date'], 'm/d/Y');
                            $time = $start_time . ' - ' . $end_time;
                            unset($val['start_date']);
                            unset($val['end_date']);
                            unset($val['id']);
                            $val['date'] = $date;
                            $val['time'] = $time;
                            $val['tags'] .= ','.$val['speakers'].',OpenStack Summit Austin';
                            unset($val['speakers']);
                            $active_sheet->fromArray($val, NULL, 'A'.$row);
                        }
                    }

                    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

                    header('Content-type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment; filename="'.$filename.'"');

                    $objWriter->save('php://output');

                    return;
                    break;
            }
            return $this->notFound();
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