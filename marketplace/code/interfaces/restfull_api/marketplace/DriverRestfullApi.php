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

use Openstack\Annotations as CustomAnnotation;

final class DriverRestfullApi extends AbstractRestfulJsonApi
{
    /**
     * @var IMarketPlaceDriverRepository
     */
    private $repository;

    /**
     * DriverResfullApi constructor.
     * @param IMarketPlaceDriverRepository $repository
     */
    public function __construct
    (
        IMarketPlaceDriverRepository $repository
    )
    {
        parent::__construct();
        $this->repository = $repository;
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
        return true;
    }

    /**
     * @return bool
     */
    protected function authenticate(){
        return true;
    }

    static $url_handlers = array(
        'GET '  => 'getDrivers',
    );

    static $allowed_actions = [
       'getDrivers',
    ];


    public function getDrivers(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $sort_field   = (isset($query_string['sort_field']) ? Convert::raw2sql($query_string['sort_field']) : 'Project');
            $sort_dir     = (isset($query_string['sort_dir']) ? Convert::raw2sql($query_string['sort_dir']) : 'ASC');
            $order        = $sort_field.' '.$sort_dir;
            $filters['project'] = (boolval(isset($query_string['project'])) ? Convert::raw2sql($query_string['project']) : 'all');
            $filters['release'] = (boolval(isset($query_string['release'])) ? Convert::raw2sql($query_string['release']) : 'all');
            $filters['vendor'] = (boolval(isset($query_string['vendor'])) ? Convert::raw2sql($query_string['vendor']) : 'all');

            $list = $this->repository->getAllByFilter($order, $filters);

            $items = [];

            foreach ($list as $item){
                $driver = Driver::get()->byID($item->ID);
                $releases = [];
                if ($driver) {
                    foreach ($driver->Releases() as $release) {
                        $releases[] = [
                            'id' => $release->ID,
                            'url' => $release->Url,
                            'name' => $release->Name,
                        ];
                    }
                }

                $items[] =
                [
                    'id'            => intval($item->ID),
                    'name'          => $item->Name,
                    'url'           => $item->Url,
                    'description'   => trim($item->Description),
                    'project'       => trim($item->Project),
                    'vendor'        => trim($item->Vendor),
                    'driver'        => trim($item->Driver),
                    'releases'      => $releases
                ];
            }

            return $this->ok($items);
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