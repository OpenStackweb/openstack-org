<?php

/**
 * Copyright 2016 OpenStack Foundation
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
final class SummitAppRegistrationCodesApi extends AbstractRestfulJsonApi
{
    /**
     * @var IEntityRepository
     */
    private $summit_repository;

    /**
     * @var ISummitRegistrationPromoCodeRepository
     */
    private $code_repository;

    /**
     * @var ISummitService
     */
    private $summit_service;

    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitRegistrationPromoCodeRepository $code_repository,
        ISummitService $summit_service
    )
    {
        parent::__construct();
        $this->summit_repository        = $summit_repository;
        $this->code_repository          = $code_repository;
        $this->summit_service           = $summit_service;
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

    static $url_handlers = array(
        'GET sponsors/all'        => 'getRegistrationSponsors',
        'POST sponsors'           => 'addRegistrationSponsor',
        'PUT sponsors/$ORG_ID!'   => 'updateRegistrationSponsor',
        'GET all'                 => 'getRegistrationCodes',
        'GET free'                => 'getFreeRegistrationCodes',
        'GET export'              => 'exportRegistrationCodes',
        'GET $REG_CODE!'          => 'getRegistrationCodeByTerm',
        'POST bulk'               => 'setBulkRegistrationCodes',
        'POST '                   => 'addRegistrationCode',
        'PUT $REG_CODE!'          => 'updateRegistrationCode',
        'DELETE $REG_CODE!'       => 'deleteRegistrationCode',
    );

    static $allowed_actions = array(
        'getRegistrationCodeByTerm',
        'getRegistrationCodes',
        'addRegistrationCode',
        'updateRegistrationCode',
        'deleteRegistrationCode',
        'getRegistrationSponsors',
        'addRegistrationSponsor',
        'updateRegistrationSponsor',
        'getFreeRegistrationCodes',
        'setBulkRegistrationCodes',
        'exportRegistrationCodes',
    );

    public function getRegistrationCodeByTerm(SS_HTTPRequest $request) {
        try
        {
            $term         = Convert::raw2sql($request->param('REG_CODE'));
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $codes = SpeakerSummitRegistrationPromoCode::get()->filter
            (
                array
                (
                    'SummitID'  => $summit_id,
                    'OwnerID'   => 0,
                    'SpeakerID' => 0,
                )
            )->where(" Code LIKE '{$term}%' ")->limit(25,0);

            $data = array();
            foreach ($codes as $code) {

                $data[] = array
                (
                    'code' => trim($code->Code),
                    'name' => sprintf('%s (%s)', $code->Code, $code->Type )
                );
            }
            return $this->ok($data, false);
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

    public function getRegistrationCodes(SS_HTTPRequest $request) {
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? intval(Convert::raw2sql($query_string['page'])) : '';
            $page_size    = (isset($query_string['items'])) ? intval(Convert::raw2sql($query_string['items'])) : '';
            $term         = (isset($query_string['term'])) ? trim(Convert::raw2sql($query_string['term'])) : '';
            $type         = (isset($query_string['type'])) ? trim(Convert::raw2sql($query_string['type'])) : '';
            $sort_by      = (isset($query_string['sort_by'])) ? trim(Convert::raw2sql($query_string['sort_by'])) : '';
            $sort_dir     = (isset($query_string['sort_dir'])) ? trim(Convert::raw2sql($query_string['sort_dir'])) : '';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            list($page, $page_size, $count, $codes) = $this->code_repository->searchByTermAndSummitPaginated
                (
                    $summit_id,
                    $type,
                    $page,
                    $page_size,
                    $term,
                    $sort_by,
                    $sort_dir
                );

            return $this->ok(array('page' => $page, 'page_size' => $page_size, 'count' => $count, 'codes' => $codes));
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

    public function getFreeRegistrationCodes(SS_HTTPRequest $request) {
        try
        {
            $query_string = $request->getVars();
            $limit        = (isset($query_string['limit'])) ? intval(Convert::raw2sql($query_string['limit'])) : '';
            $prefix       = (isset($query_string['prefix'])) ? trim(Convert::raw2sql($query_string['prefix'])) : '';
            $type         = (isset($query_string['type'])) ? trim(Convert::raw2sql($query_string['type'])) : '';
            $company_id   = (isset($query_string['company_id'])) ? trim(Convert::raw2sql($query_string['company_id'])) : '';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $codes = $this->code_repository->getFreeByTypeAndSummit($summit_id,$type,$prefix,$company_id,$limit);

            return $this->ok($codes->toNestedArray());
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

    public function addRegistrationCode(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $data         = $this->getJsonRequest();
            $promocode = $this->summit_service->createPromoCode($summit, $data);
            return $this->ok($promocode->getCode());
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(EntityAlreadyExistsException $ex3)
        {
            SS_Log::log($ex3->getMessage(), SS_Log::WARN);
            return $this->validationError(array(
                array('message' => $ex3->getMessage())
            ));
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function updateRegistrationCode(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $code_id   = intval($request->param('REG_CODE'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $data         = $this->getJsonRequest();
            $data['code_id'] = $code_id;
            $promocode = $this->summit_service->updatePromoCode
                (
                    $summit,
                    $data
                );
            return $this->ok($promocode);
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
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

    public function deleteRegistrationCode(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $code_id   = intval($request->param('REG_CODE'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $promocode  = $this->code_repository->getById($code_id);
            if(is_null($promocode)) throw new NotFoundEntityException('PromoCode');

            if ($promocode->EmailSent)
                throw new EntityValidationException("Cannot delete a code that has been already sent.");

            $promocode->delete();

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
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getRegistrationSponsors(SS_HTTPRequest $request) {
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? intval(Convert::raw2sql($query_string['page'])) : '';
            $page_size    = (isset($query_string['items'])) ? intval(Convert::raw2sql($query_string['items'])) : '';
            $term         = (isset($query_string['term'])) ? trim(Convert::raw2sql($query_string['term'])) : '';
            $sort_by      = (isset($query_string['sort_by'])) ? trim(Convert::raw2sql($query_string['sort_by'])) : '';
            $sort_dir     = (isset($query_string['sort_dir'])) ? trim(Convert::raw2sql($query_string['sort_dir'])) : '';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            list($page, $page_size, $count, $sponsors) = $this->code_repository->searchSponsorByTermAndSummitPaginated
                (
                    $summit_id,
                    $page,
                    $page_size,
                    $term,
                    $sort_by,
                    $sort_dir
                );

            return $this->ok(array('page' => $page, 'page_size' => $page_size, 'count' => $count, 'codes' => $sponsors));
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

    public function addRegistrationSponsor(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $data         = $this->getJsonRequest();
            $promocode = $this->summit_service->createPromoCode($summit, $data);
            return $this->ok($promocode->getSponsor()->ID);
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(EntityAlreadyExistsException $ex3)
        {
            SS_Log::log($ex3->getMessage(), SS_Log::WARN);
            return $this->validationError(array(
                array('message' => $ex3->getMessage())
            ));
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function updateRegistrationSponsor(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $data         = $this->getJsonRequest();
            $promocode = $this->summit_service->createPromoCode($summit, $data);
            return $this->ok($promocode->getSponsor()->ID);
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
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

    public function setBulkRegistrationCodes(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $data         = $this->getJsonRequest();

            $promocodes = $this->summit_service->setMultiPromoCodes($summit, $data);
            $code_array = array();
            foreach ($promocodes as $code) {
                $code_array[] = $code->Code;
            }

            return $this->ok($code_array);
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(EntityAlreadyExistsException $ex3)
        {
            SS_Log::log($ex3->getMessage(), SS_Log::WARN);
            return $this->validationError(array(
                array('message' => $ex3->getMessage())
            ));
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function exportRegistrationCodes(SS_HTTPRequest $request) {
        try {

            $query_string = $request->getVars();
            $code_type = (isset($query_string['type'])) ? $query_string['type'] : '';
            $term = (isset($query_string['term'])) ? $query_string['term'] : '';
            $summit_id = intval($request->param('SUMMIT_ID'));
            $summit = $this->summit_repository->getById($summit_id);
            if (is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $ext = 'csv';

            list($page, $page_size, $count, $codes) = $this->code_repository->searchByTermAndSummitPaginated
                (
                    $summit_id,
                    $code_type,
                    1,
                    0,
                    $term
                );

            $results = array();
            foreach ($codes as $row) {
                $code_array = array(
                    'Code' => $row['code'],
                    'Type' => $row['type'],
                    'Owner' => $row['owner'],
                    'Email' => $row['owner_email'],
                    'Sponsor' => $row['org'],
                    'Emailed' => $row['email_sent'],
                    'Redeemed' => $row['redeemed']
                );

                if (!$code_type) {
                    $this_type = $row['type'];
                    if (!isset($results[$this_type])) $results[$this_type] = array();
                    $results[$this_type][] = $code_array;
                } else {
                    array_push($results, $code_array);
                }
            }

            $filename = "promocodes_report-" . date('Ymd') . "." . $ext;
            $delimiter = ($ext == 'xls') ? "\t" : ",";

            if (!$code_type) { // if we export all codes we put each type on a different sheet
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("OpenStack");
                $objPHPExcel->getProperties()->setTitle("PromoCodes");
                $sheet_no = 0;
                foreach ($results as $type => $codes) {
                    if($sheet_no > 0){
                        $active_sheet = $objPHPExcel->createSheet();
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0);
                        $active_sheet = $objPHPExcel->getActiveSheet();
                    }
                    $sheet_title = ($type) ? $type : 'No Type';
                    $active_sheet->setTitle($sheet_title);
                    $active_sheet->fromArray(array('Code','Type','Owner','Email','Sponsor','Emailed','Redeemed'), NULL, 'A1');
                    foreach ($codes as $key => $code) {
                        $row = $key + 2;
                        $active_sheet->fromArray($code, NULL, 'A'.$row);
                    }

                    $sheet_no++;
                }
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

                header('Content-type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="'.$filename.'"');

                $objWriter->save('php://output');

                return;
            } else {
                return CSVExporter::getInstance()->export($filename, $results, $delimiter);
            }
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