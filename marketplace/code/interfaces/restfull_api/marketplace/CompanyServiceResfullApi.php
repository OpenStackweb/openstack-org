<?php
/**
 * Copyright 2017 OpenStack Foundation
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
final class CompanyServiceResfullApi extends AbstractRestfulJsonApi
{
    /**
     * @var IRegionalServiceRepository
     */
    private $regional_repository;

    /**
     * CompanyServiceResfullApi constructor.
     * @param IRegionalServiceRepository $repository
     */
    public function __construct
    (
        IRegionalServiceRepository $repository
    )
    {
        parent::__construct();
        $this->regional_repository = $repository;
    }

    const ApiPrefix = 'api/v1/marketplace';

    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        //check permissions
        if(!Permission::check("SANGRIA_ACCESS"))
            return false;
        return true;
    }

    static $url_handlers = array(
        'GET regional/export/csv'   => 'getRegionalCompanyServicesExport',
        'GET regional'              => 'getRegionalCompanyServices'
    );

    static $allowed_actions = [
       'getRegionalCompanyServices',
       'getRegionalCompanyServicesExport'
    ];

    public function getRegionalCompanyServices(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = intval((isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : 0);
            $page_size    = intval((isset($query_string['page_size'])) ? Convert::raw2sql($query_string['page_size']) : 25);

            // zero mean showing all ...
            if($page_size == 0) $page_size = PHP_INT_MAX;

            $order        = (isset($query_string['order'])) ? Convert::raw2sql($query_string['order']) : '';
            $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';
            $service_region = (isset($query_string['region'])) ? Convert::raw2sql($query_string['region']) : '';
            $service_type = (isset($query_string['type'])) ? Convert::raw2sql($query_string['type']) : '';
            $show_all     = boolval((isset($query_string['show_all'])) ? Convert::raw2sql($query_string['show_all']) : true);

            list($list, $count) = $this->regional_repository->getAllByPage(
                $page,
                $page_size,
                $order,
                $show_all,
                $search_term,
                $service_type,
                $service_region
            );

            $items = [];

            foreach ($list as $item){
                $regions = implode(',',array_unique(explode(', ', $item->RegionName)));
                $items[] =
                [
                    'id'                   => intval($item->ID),
                    'name'                 => trim($item->Name),
                    'type'                 => trim($item->ClassName),
                    'company'              => trim($item->CompanyName),
                    'program_version_id'   => intval($item->ProgramVersionID),
                    'city'                 => $item->City,
                    'country'              => $item->Country,
                    'region'               => $regions,
                    'admins'               => $item->Admins,
                    'notes'                => trim($item->Notes)
                ];
            }

            return $this->ok(array('items' => $items, 'count' => $count));
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

    public function getRegionalCompanyServicesExport(SS_HTTPRequest $request){
        $data         = [];
        $filename     = "CopanyServicesRegional" . date('Ymd') . ".csv";
        $query_string = $request->getVars();
        $order        = (isset($query_string['order'])) ? Convert::raw2sql($query_string['order']) : '';
        $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';
        $service_type = (isset($query_string['type'])) ? Convert::raw2sql($query_string['type']) : '';
        $show_all     = boolval((isset($query_string['show_all'])) ? Convert::raw2sql($query_string['show_all']) : true);

        list($list, $count) = $this->regional_repository->getAllByPage
        (
            1,
            PHP_INT_MAX,
            $order,
            $show_all,
            $search_term,
            $service_type
        );

        foreach ($list as $item){

            $regions = implode(',',array_unique(explode(', ', $item->RegionName)));
            $data[] =
                [
                    'Id'                   => intval($item->ID),
                    'Service'              => trim($item->Name),
                    'Type'                 => trim($item->ClassName),
                    'Company'              => trim($item->CompanyName),
                    'Program Version Name' => intval($item->ProgramVersionName),
                    'City'                 => $item->City,
                    'Country'              => $item->Country,
                    'Region'               => $regions,
                    'Admins'               => $item->Admins,
                    'Notes'                => trim($item->Notes)
                ];
        }

        return CSVExporter::getInstance()->export($filename, $data, ',');
    }


}