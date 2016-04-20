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
     * @var ISummitService
     */
    private $summit_service;

    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitAssistanceRepository $assistance_repository,
        ISummitService $summit_service
    )
    {
        parent::__construct();
        $this->assistance_repository         = $assistance_repository;
        $this->summit_repository             = $summit_repository;
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
        'GET speaker_report'        => 'getSpeakerReport',
        'GET room_report'           => 'getRoomReport',
        'GET presentation_report'   => 'getPresentationReport',
        'GET export/$REPORT!'       => 'exportReport',
        'PUT save_report/$REPORT!'  => 'updateReport',
    );

    static $allowed_actions = array(
        'getSpeakerReport',
        'getRoomReport',
        'getPresentationReport',
        'exportReport',
        'updateReport',
    );

    public function getSpeakerReport(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : '';
            $page_size    = (isset($query_string['items'])) ? Convert::raw2sql($query_string['items']) : '';
            $sort         = (isset($query_string['sort'])) ? Convert::raw2sql($query_string['sort']) : 'ID';
            $sort_dir     = (isset($query_string['sort_dir'])) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $assistances = $this->assistance_repository->getAssistanceBySummit($summit_id,$page,$page_size,$sort,$sort_dir);

            $assistance_array = array();

            foreach($assistances['Data'] as $assistance) {
                $assistance_array[] = array(
                    'id'            => $assistance['id'],
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

            echo json_encode(array('total' => $assistances['Total'], 'data' => $assistance_array));
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
            $venue        = (isset($query_string['venue'])) ? Convert::raw2sql($query_string['venue']) : 'all';

            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $days = $summit->getDates();

            $report_array = array();

            foreach($days as $day) {
                $day_report = $this->assistance_repository->getRoomsBySummitAndDay($summit_id,$day->Date,$event_type,$venue);
                $report_array[$day->Label] = array();
                foreach ($day_report as $rooms) {

                    $report_array[$day->Label][] = array(
                        'id'         => intVal($rooms['id']),
                        'start_time' => $summit->convertDateFromUTC2TimeZone($rooms['start_date'],'g:ia'),
                        'end_time'   => $summit->convertDateFromUTC2TimeZone($rooms['end_date'],'g:ia'),
                        'code'       => 'K',
                        'title'      => $rooms['event'],
                        'room'       => $rooms['room'],
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

            echo json_encode($return_array);
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
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $presentations = $this->assistance_repository->getPresentationsAndSpeakersBySummit($summit_id,$page,$page_size,$sort,$sort_dir,$search_term);

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
                    'name'            => empty($presentation['speaker_name'])? $presentation['member_name'] : $presentation['speaker_name'],
                    'email'           => $presentation['email'],
                    'phone'           => $presentation['phone'],
                    'code_type'       => $presentation['code_type'],
                    'promo_code'      => $presentation['promo_code'],
                    'confirmed'       => intVal($presentation['confirmed']),
                    'registered'      => intVal($presentation['registered']),
                    'checked_in'      => intVal($presentation['checked_in'])
                );
            }

            echo json_encode(array('total' => $presentations['Total'], 'data' => $presentation_array));
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

    public function exportReport(SS_HTTPRequest $request) {
        try
        {

            $query_string = $request->getVars();
            $sort         = (isset($query_string['sort'])) ? Convert::raw2sql($query_string['sort']) : 'name';
            $sort_dir     = (isset($query_string['sort_dir'])) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC';
            $event_type   = (isset($query_string['event_type'])) ? Convert::raw2sql($query_string['event_type']) : 'all';
            $venue        = (isset($query_string['venue'])) ? Convert::raw2sql($query_string['venue']) : 'all';
            $report       = $request->param('REPORT');
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            switch ($report) {
                case 'speaker_report' :
                    $report_data = $this->assistance_repository->getAssistanceBySummit($summit_id,null,null,$sort,$sort_dir);
                    $report_data = $report_data['Data'];
                    $header = array('Speaker ID', 'Member ID', 'Name', 'Email', 'Phone On Site', 'Company',
                                    'Presentation', 'Track', 'Confirmed?', 'Registered?', 'Checked In');

                    $csv = implode(',',$header).PHP_EOL;
                    foreach($report_data as $val) {
                        $val = array_slice($val, 1); //skip id
                        //escape commas
                        foreach($val as &$col) {
                            $col = '"'.str_replace("\"","'" , $col ).'"';
                        }
                        $csv .= implode(',',$val).PHP_EOL;
                    }
                    break;
                case 'room_report' :
                    $csv = '';
                    $days = $summit->getDates();
                    $header = array('Time', 'Code', 'Presentation', 'Room', 'Speakers','Head Count', 'Total');
                    foreach($days as $day) {
                        $csv .= $day->Label.PHP_EOL;
                        $day_report = $this->assistance_repository->getRoomsBySummitAndDay($summit_id,$day->Date,$event_type,$venue);
                        $csv .= implode(',',$header).PHP_EOL;
                        foreach($day_report as $val) {
                            $start_date = $summit->convertDateFromUTC2TimeZone($val['start_date'],'g:ia');
                            $end_date = $summit->convertDateFromUTC2TimeZone($val['start_date'],'g:ia');

                            $time = $start_date.' - '.$end_date;
                            unset($val['start_date']);
                            unset($val['end_date']);
                            //escape commas
                            foreach($val as &$col) {
                                $col = '"'.str_replace("\"","'" , $col ).'"';
                            }
                            array_unshift($val,$time);
                            $csv .= implode(',',$val).PHP_EOL;
                        }
                    }
                    break;
                case 'presentation_report' :
                    $search_term  = (isset($query_string['term'])) ? Convert::raw2sql($query_string['term']) : '';
                    $report_data  = $this->assistance_repository->getPresentationsAndSpeakersBySummit($summit_id,null,null,$sort,$sort_dir,$search_term);
                    $data         = $report_data['Data'];
                    $results      = array();
                    foreach($data as $row)
                    {
                        unset($row['presentation_id']);
                        unset($row['assistance_id']);
                        array_push($results, $row);
                    }

                    $ext = 'csv';
                    $filename = "presentations_report-" . date('Ymd') . "." . $ext;
                    $delimiter = ($ext == 'xls') ? "\t" : "," ;
                    return CSVExporter::getInstance()->export($filename, $results, $delimiter);
                    break;
            }

            echo $csv;

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

    public function buildCSV($header,$data) {
        $csv = implode(',',$header).PHP_EOL;
        foreach($data as $val) {
            $val = array_slice($val, 1); //skip id
            $csv .= implode(',',$val).PHP_EOL;
        }

        return $csv;
    }
}