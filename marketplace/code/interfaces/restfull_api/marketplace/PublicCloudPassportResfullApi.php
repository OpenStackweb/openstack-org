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
final class PublicCloudPassportResfullApi extends AbstractRestfulJsonApi
{
    /**
     * @var IPublicCloudPassportRepository
     */
    private $repository;

    /**
     * @var IPublicCloudPassportManager
     */
    private $manager;

    /**
     * PublicCloudPassportResfullApi constructor.
     * @param IPublicCloudPassportRepository $repository
     * @param IPublicCloudPassportManager $manager
     */
    public function __construct
    (
        IPublicCloudPassportRepository $repository,
        IPublicCloudPassportManager $manager
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->manager    = $manager;
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
    protected function authenticate(){
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        return true;
    }

    static $url_handlers = array(
        'GET public_clouds'                          => 'getPublicClouds',
        'GET '                                       => 'getPassports',
        'PUT $SERVICE_ID!'                           => 'updatePublicCloudPassport',
    );

    static $allowed_actions = [
       'getPublicClouds',
       'getPassports',
       'updatePublicCloudPassport',
    ];

    public function getPublicClouds(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = intval((isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : 0);
            $page_size    = intval((isset($query_string['page_size'])) ? Convert::raw2sql($query_string['page_size']) : 25);

            // zero mean showing all ...
            if($page_size == 0) $page_size = PHP_INT_MAX;

            $order        = (isset($query_string['order'])) ? Convert::raw2sql($query_string['order']) : '';
            $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';

            list($list, $count) = $this->repository->getAllCloudsByPage(
                $page,
                $page_size,
                $order,
                $search_term
            );

            $items = [];

            foreach ($list as $item){
                $items[] =
                [
                    'id'                   => intval($item->CloudID),
                    'name'                 => trim($item->CloudName),
                    'company'              => trim($item->CompanyName),
                    'is_passport'          => intval($item->Active),
                    'learn_more'           => $item->LearnMore,
                    'slug'                 => $item->CompanySlug.'/'.$item->CloudSlug
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

    public function getPassports(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $start   = intval((isset($query_string['start'])) ? Convert::raw2sql($query_string['start']) : 0);
            $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';

            list($list, $total) = $this->repository->getAllPassports(
                $start,
                10,
                null,
                $search_term
            );

            $has_more = ($start + 10) < $total;
            $items = [];

            $public_clouds_link = PublicCloudsDirectoryPage::get()->first()->Link();

            foreach ($list as $item){
                $locations =[];
                $lats = explode(',', $item->Lat);
                $lngs = explode(',', $item->Lng);

                foreach ($lats as $idx => $lat) {
                    $locations[] = [
                        'id'                => intval($item->CloudID).'_'.$idx,
                        'item_id'           => intval($item->CloudID),
                        'name'              => $item->CloudName,
                        'lat'               => floatval($lat),
                        'lng'               => floatval($lngs[$idx]),
                        'isInfoWindowOpen'  => false
                    ];
                }

                $items[] =
                    [
                        'id'                => intval($item->CloudID),
                        'name'              => trim($item->CloudName),
                        'company'           => trim($item->CompanyName),
                        'description'       => trim($item->CloudOverview),
                        'logo'              => $item->Logo,
                        'location_string'   => $item->Locations,
                        'is_passport'       => intval($item->Active),
                        'learn_more'        => $item->LearnMore,
                        'slug'              => $public_clouds_link.$item->CompanySlug.'/'.$item->CloudSlug,
                        'date'              => $item->LastEdited,
                        'locations'         => $locations
                    ];

            }

            return $this->ok(array('items' => $items, 'total' => $total, 'has_more' => $has_more));
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

    public function updatePublicCloudPassport(SS_HTTPRequest $request){
        try
        {
            if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
            $service_id  = intval($request->param('SERVICE_ID'));
            $data        = $this->getJsonRequest();

            $this->manager->updatePublicCloudPassport($data, $service_id);
            return $this->updated();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }



}