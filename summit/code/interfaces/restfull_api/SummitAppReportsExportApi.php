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
class SummitAppReportsExportApi extends AbstractRestfulJsonApi {


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
        'GET speaker_report'                => 'exportSpeakerReport',
        'GET room_report'                   => 'exportRoomReport',
        'GET presentation_report'           => 'exportPresentationReport',
        'GET video_report'                  => 'exportVideoReport',
        'GET rsvp_report'                   => 'exportRsvpReport',
        'GET presentations_company_report'  => 'exportPresentationsCompanyReport',
        'GET presentations_by_track_report' => 'exportPresentationsByTrackReport',
    );

    static $allowed_actions = array(
        'exportSpeakerReport',
        'exportRoomReport',
        'exportPresentationReport',
        'exportVideoReport',
        'exportReport',
        'exportRsvpReport',
        'exportPresentationsCompanyReport',
        'exportPresentationsByTrackReport',
    );

    public function exportSpeakerReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $sort = (isset($query_string['sort'])) ? Convert::raw2sql($query_string['sort']) : 'name';
            $sort_dir = (isset($query_string['sort_dir'])) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC';
            $filter = (isset($query_string['filter'])) ? $query_string['filter'] : 'all';
            $summit_id = intval($request->param('SUMMIT_ID'));
            $summit = $this->summit_repository->getById($summit_id);
            if (is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $ext = 'csv';

            $report_data = $this->assistance_repository->getAssistanceBySummit($summit_id, null, null, $sort, $sort_dir, $filter);
            $data = $report_data['Data'];
            $results = array();
            foreach ($data as $row) {
                array_push($results, $row);
            }
            $filename = "speaker_report-" . date('Ymd') . "." . $ext;
            $delimiter = ($ext == 'xls') ? "\t" : ",";
            return CSVExporter::getInstance()->export($filename, $results, $delimiter);

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

    public function exportRoomReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $event_type = (isset($query_string['event_type'])) ? Convert::raw2sql($query_string['event_type']) : 'all';
            $venues = (isset($query_string['venues'])) ? $query_string['venues'] : '';
            $summit_id = intval($request->param('SUMMIT_ID'));
            $summit = $this->summit_repository->getById($summit_id);
            if (is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

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

    public function exportVideoReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $venues = (isset($query_string['venues'])) ? $query_string['venues'] : '';
            $tracks = (isset($query_string['tracks'])) ? html_entity_decode($query_string['tracks']) : 'all';
            $start_date = (isset($query_string['start_date']) && $query_string['start_date']) ? date('Y-m-d',strtotime($query_string['start_date'])) : '';
            $end_date = (isset($query_string['end_date']) && $query_string['end_date']) ? date('Y-m-d',strtotime($query_string['end_date'])) : '';
            $search_term = (isset($query_string['term'])) ? $query_string['term'] : '';
            $summit_id = intval($request->param('SUMMIT_ID'));
            $summit = $this->summit_repository->getById($summit_id);
            if (is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

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

                $day_report = $this->presentation_repository->getPresentationMaterialBySummitAndDay($summit_id,$day->Date,$tracks,$venues,$start_date,$end_date,$search_term);

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

            $objPHPExcel->removeSheetByIndex(0);
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            $objWriter->save('php://output');

            return;
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

    public function exportPresentationReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $sort = (isset($query_string['sort'])) ? Convert::raw2sql($query_string['sort']) : 'name';
            $sort_dir = (isset($query_string['sort_dir'])) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC';
            $search_term = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
            $filter = (isset($query_string['filter'])) ? $query_string['filter'] : 'all';
            $summit_id = intval($request->param('SUMMIT_ID'));
            $summit = $this->summit_repository->getById($summit_id);
            if (is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $ext = 'csv';

            $report_data = $this->presentation_repository->getPresentationsAndSpeakersBySummit($summit_id, null, null, $sort, $sort_dir, $search_term,$filter);
            $data = $report_data['Data'];
            $results = array();
            foreach ($data as $row) {
                $row['Start_Date'] = $summit->convertDateFromUTC2TimeZone($row['Start_Date'],'m/d/Y g:ia');
                unset($row['Assistance_id']);
                array_push($results, $row);
            }
            $filename = "presentations_report-" . date('Ymd') . "." . $ext;
            $delimiter = ($ext == 'xls') ? "\t" : ",";
            return CSVExporter::getInstance()->export($filename, $results, $delimiter);
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

    public function exportRsvpReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $search_term = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
            $summit_id = intval($request->param('SUMMIT_ID'));
            $summit = $this->summit_repository->getById($summit_id);
            if (is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $events  = $this->event_repository->searchBySummitTermAndHasRSVP($summit,$search_term);

            if (count($events)) {

                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("OpenStack");
                $objPHPExcel->getProperties()->setTitle("RSVP per Event Report");

                foreach($events as $event) {
                    $room_cap = ($event->Location() && $event->Location()->is_a('SummitVenueRoom')) ? $event->Location()->Capacity : 0;
                    list($regulars,$reg_count) = $this->rsvp_repository->getByEventAndType($event->ID, 'Regular');
                    list($waitlists,$wait_count) = $this->rsvp_repository->getByEventAndType($event->ID, 'WaitList');
                    list($rsvps,$total) = $this->rsvp_repository->getByEventPaged($event->ID,null,null);
                    $rsvp_array_template = array();
                    $headers = array();

                    $active_sheet = $objPHPExcel->createSheet();
                    $active_sheet->setTitle('Event '.$event->Location());

                    foreach ($event->RSVPTemplate()->Questions()->sort('Order') as $question) {
                        if ($question->Label) {
                            $rsvp_array_template[$question->Label] = '';
                            $headers[] = $question->Label;
                        }
                    }
                    $headers[] = 'Seat Type';

                    $active_sheet->setCellValue('A1', $event->getTitleAndTime());
                    $active_sheet->mergeCells('A1:K1');
                    $active_sheet->setCellValue('A3', 'Room Total:');
                    $active_sheet->setCellValue('B3', $room_cap);
                    $active_sheet->setCellValue('A4', 'RSVP #:');
                    $active_sheet->setCellValue('B4', $reg_count);
                    $active_sheet->setCellValue('C4', 'WaitList #:');
                    $active_sheet->setCellValue('D4', $wait_count);

                    $active_sheet->fromArray($headers, NULL, 'A6');
                    $active_sheet->getStyle("A6:K6")->getFont()->setBold(true);

                    if (count($rsvps)) {
                        foreach($rsvps as $key => $rsvp) {
                            $row = $key + 7;
                            $rsvp_array = $rsvp_array_template;

                            foreach ($rsvp->Answers() as $answer) {
                                $rsvp_array[$answer->Question()->Label] = $answer->getFormattedAnswer();
                            }

                            $rsvp_array['Seat Type'] = $rsvp->SeatType;

                            $active_sheet->fromArray($rsvp_array, NULL, 'A'.$row);
                        }
                    }
                }

                $filename = (count($events) == 1) ?  $event->getTitleForUrl()."-".date('Ymd').".xlsx" : "rsvp_report-" . date('Ymd') . ".xlsx";

                $objPHPExcel->removeSheetByIndex(0);
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

                header('Content-type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                $objWriter->save('php://output');

                return;
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
            return $ex->getMessage();
        }
    }

    public function exportPresentationsCompanyReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $sort = (isset($query_string['sort'])) ? Convert::raw2sql($query_string['sort']) : 'name';
            $sort_dir = (isset($query_string['sort_dir'])) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC';
            $search_term = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
            $summit_id = intval($request->param('SUMMIT_ID'));
            $summit = $this->summit_repository->getById($summit_id);
            if (is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $ext = 'csv';

            $report_data = $this->presentation_repository->searchByCompanyPaged($summit_id, null, null, $sort, $sort_dir, $search_term);
            $filename = "presentations_company_report-" . date('Ymd') . "." . $ext;
            $delimiter = ($ext == 'xls') ? "\t" : ",";
            return CSVExporter::getInstance()->export($filename, $report_data['Data'], $delimiter);
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

    public function exportPresentationsByTrackReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : '';
            $page_size    = (isset($query_string['items'])) ? Convert::raw2sql($query_string['items']) : '';
            $sort         = (isset($query_string['sort'])) ? Convert::raw2sql($query_string['sort']) : 'presentation';
            $sort_dir     = (isset($query_string['sort_dir'])) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC';
            $search_term  = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
            $filters['status']    = (isset($query_string['status'])) ? Convert::raw2sql($query_string['status']) : '';
            $filters['published'] = (isset($query_string['published'])) ? Convert::raw2sql($query_string['published']) : '';
            $filters['track']     = (isset($query_string['track']) && $query_string['track']) ? explode(',',$query_string['track']) : array();
            $filters['show_col']  = (isset($query_string['show_col']) && $query_string['show_col']) ? explode(',',$query_string['show_col']) : array();

            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $report_data = $this->presentation_repository->searchByTrackPaged($summit_id,$page,$page_size,$sort,$sort_dir,$search_term,$filters);
            $data = $report_data['data'];
            $track_count = $report_data['track_count'];

            if (count($data)) {

                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("OpenStack");
                $objPHPExcel->getProperties()->setTitle("Presentations By Track Report");
                $active_sheet = $objPHPExcel->getActiveSheet();
                $track_index = 0;
                foreach($track_count as $track => $count) {
                    $row = $track_index + 2;
                    $active_sheet->fromArray(array('Track','Count'), NULL, 'A1');
                    $active_sheet->fromArray(array($track,$count), NULL, 'A'.$row);
                    $track_index++;
                }

                $show_header = true;
                foreach($data as $key => $event) {
                    $row2 = $row + $key + 3;

                    if ($show_header) {
                        $heather_row = $row2 - 1;
                        $headers = array_keys($event);
                        $active_sheet->fromArray($headers, NULL, 'A'.$heather_row);
                        $show_header = false;
                    }

                    $active_sheet->fromArray($event, NULL, 'A'.$row2);
                }

                $filename = "presentation_by_track_report-" . date('Ymd') . ".xlsx";

                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

                header('Content-type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                $objWriter->save('php://output');

                return;
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