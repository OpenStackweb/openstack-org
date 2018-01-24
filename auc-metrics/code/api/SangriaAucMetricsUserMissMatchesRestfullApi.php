<?php
/**
 * Copyright 2018 OpenStack Foundation
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

/**
 * Class SangriaAucMetricsUserMissMatchesRestfullApi
 */
final class SangriaAucMetricsUserMissMatchesRestfullApi extends AbstractRestfulJsonApi
{
    /**
     * @return bool
     */
    public function authorize(){
        //check permissions
        if(!Permission::check("SANGRIA_ACCESS"))
            return false;
        return true;
    }

    /**
     * @var IAUCMetricMissMatchErrorRepository
     */
    private $repository;

    /**
     * @var IAUCMetricService
     */
    private $service;

    /**
     * SangriaAucMetricsUserMissMatchesRestfullApi constructor.
     * @param IAUCMetricMissMatchErrorRepository $repository
     * @param IAUCMetricService $service
     */
    public function __construct
    (
        IAUCMetricMissMatchErrorRepository $repository,
        IAUCMetricService $service
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->service    = $service;
    }

    static $url_handlers = [
        'PUT $MISSMATCH_ID!'    => 'resolveUserMissMatch',
        'DELETE $MISSMATCH_ID!' => 'deleteUserMissMatch',
        'GET '                  => 'getUserUserMissMatches',
    ];

    static $allowed_actions = [
        'resolveUserMissMatch',
        'deleteUserMissMatch',
        'getUserUserMissMatches'
    ];

    public function resolveUserMissMatch(SS_HTTPRequest $request){
        try {
            if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
            $error_id  = intval($request->param('MISSMATCH_ID'));
            $data      = $this->getJsonRequest();

            $this->service->fixMissMatchUserError($error_id, intval($data['member_id']));
            return $this->updated();
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

    public function deleteUserMissMatch(SS_HTTPRequest $request){
        try {
            $error_id  = intval($request->param('MISSMATCH_ID'));
            $this->service->deleteMissMatchUserError($error_id);
            return $this->deleted();
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

    public function getUserUserMissMatches(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = intval((isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : 0);
            $page_size    = intval((isset($query_string['page_size'])) ? Convert::raw2sql($query_string['page_size']) : 25);

            // zero mean showing all ...
            if($page_size == 0) $page_size = PHP_INT_MAX;

            $order        = (isset($query_string['order'])) ? Convert::raw2sql($query_string['order']) : '';
            $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';
            $type  = (isset($query_string['type'])) ? Convert::raw2sql($query_string['type']) : '';

            list($list, $count) = $this->repository->getAllByPage(
                $page,
                $page_size,
                $order,
                $search_term,
                $type
            );

            $items = [];
            foreach ($list as $item){
                $items[] =
                    [
                        'id'                 => intval($item->ID),
                        'service_identifier' => $item->ServiceIdentifier,
                        'user_identifier'    => $item->UserIdentifier
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
}