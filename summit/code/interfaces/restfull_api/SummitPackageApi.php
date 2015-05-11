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

class SummitPackageApi extends AbstractRestfulJsonApi {

    const ApiPrefix = 'api/v1/summits/packages';

    private $repository;

    public function __construct(IEntityRepository $repository){
        parent::__construct();
        $this->repository = $repository;
    }

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

    protected function authenticate() {
        $referer = @$_SERVER['HTTP_REFERER'];
        if(empty($referer)) return false;
        //validate
        return Director::is_site_url($referer);
    }

    static $url_handlers = array(
        'GET '=> 'getAll',
    );

    static $allowed_actions = array(
        'getAll',
    );

    public function getAll(){
        try {
            $query = new QueryObject(new SummitPackage());
            $query->addOrder(QueryOrder::asc("Order"));
            list($list, $count) = $this->repository->getAll($query, 0, 99999);
            $res = array();
            foreach ($list as $package) {
                array_push($res, SummitPackageAssembler::toArray($package));
            }
            return $this->ok($res);
        }
        catch(Exception $ex) {
            SS_Log::log($ex, SS_Log::WARN);
        }
    }
}