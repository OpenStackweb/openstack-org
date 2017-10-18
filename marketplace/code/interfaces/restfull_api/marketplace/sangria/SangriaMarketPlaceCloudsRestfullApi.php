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

final class SangriaMarketPlaceCloudsRestfullApi extends AbstractRestfulJsonApi
{
    /**
     * @var ICloudServiceRepository
     */
    private $repository;

    public function __construct(ICloudServiceRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
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

    static $url_handlers = [
        'GET '           => 'getCloudsDataCenterLocations',
        'GET export/csv' => 'importAllCloudsDataCenterLocations',
    ];

    static $allowed_actions = [
        'getCloudsDataCenterLocations',
        'importAllCloudsDataCenterLocations',
    ];

    public function getCloudsDataCenterLocations(SS_HTTPRequest $request){
        try{
            $query_string = $request->getVars();
            $page         = intval((isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : 0);
            $page_size    = intval((isset($query_string['page_size'])) ? Convert::raw2sql($query_string['page_size']) : 25);

            // zero mean showing all ...
            if($page_size == 0) $page_size = PHP_INT_MAX;

            $order        = (isset($query_string['order'])) ? Convert::raw2sql($query_string['order']) : '';
            $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';
            $service_type = (isset($query_string['type'])) ? Convert::raw2sql($query_string['type']) : '';
            $filters      = [];
            list($list, $count) = $this->repository->getAllByPage(
                $page,
                $page_size,
                $order,
                $filters,
                $search_term,
                $service_type
            );

            $items = [];

            foreach ($list as $item){
                $items[] =
                    [
                        'id'               => intval($item->ID),
                        'name'             => trim($item->Name),
                        'type'             => trim($item->ClassName),
                        'company'          => trim($item->CompanyName),
                        'dc_qty'           => intval($item->DataCentersQty),
                        'dc_country_qty'   => intval($item->DataCentersCountryQty),
                        'dc_location_list' => trim($item->DataCentersLocations),
                    ];
            }

            return $this->ok(array('items' => $items, 'count' => $count));
            return $this->ok();
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


    public function importAllCloudsDataCenterLocations(SS_HTTPRequest $request){
        try{
            $query_string = $request->getVars();
            $filename     = "OpenStackClouds" . date('Ymd') . ".csv";

            $order        = (isset($query_string['order'])) ? Convert::raw2sql($query_string['order']) : '';
            $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';
            $service_type = (isset($query_string['type'])) ? Convert::raw2sql($query_string['type']) : '';
            $filters      = [];
            list($list, $count) = $this->repository->getAllByPage(
                1,
                PHP_INT_MAX,
                $order,
                $filters,
                $search_term,
                $service_type
            );
            $items = [];

            foreach ($list as $item){
                $items[] =
                    [
                        'Id'             => intval($item->ID),
                        'Name'           => trim($item->Name),
                        'Type'           => trim($item->ClassName),
                        'Company'        => trim($item->CompanyName),
                        '# DataCenters'  => intval($item->DataCentersQty),
                        '# Countries'    => intval($item->DataCentersCountryQty),
                        'Locations List' => trim($item->DataCentersLocations),
                    ];
            }

            return CSVExporter::getInstance()->export($filename, $items, ',');
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