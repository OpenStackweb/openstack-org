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
 * Class SangriaAucMetricsRestfullApi
 */
final class SangriaAucMetricsRestfullApi extends AbstractRestfulJsonApi
{

    /**
     * @var IAUCMetricRepository
     */
    private $repository;

    /**
     * SangriaAucMetricsRestfullApi constructor.
     * @param IAUCMetricRepository $repository
     */
    public function __construct(IAUCMetricRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
    * @return bool
    */
    public function authorize(){
        //check permissions
        if(!Permission::check("SANGRIA_ACCESS"))
            return false;
        return true;
    }

    static $url_handlers = [
        ' user-miss-matches' => 'handleUserMissMatches',
        'GET '               => 'getMetrics',
        'GET csv'            => 'exportMetricToCSV',
    ];

    static $allowed_actions = [
        'handleUserMissMatches',
        'getMetrics',
        'exportMetricToCSV',
    ];

    /**
     * @param SS_HTTPRequest $request
     * @return mixed|SS_HTTPResponse
     */
    function handleUserMissMatches(SS_HTTPRequest $request){
        $api = SangriaAucMetricsUserMissMatchesRestfullApi::create();
        return $api->handleRequest($request, DataModel::inst());
    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    function getMetrics(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = intval((isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : 0);
            $page_size    = intval((isset($query_string['page_size'])) ? Convert::raw2sql($query_string['page_size']) : 25);

            // zero mean showing all ...
            if($page_size == 0) $page_size = PHP_INT_MAX;

            $order        = (isset($query_string['order'])) ? Convert::raw2sql($query_string['order']) : '';
            $filter       = (isset($query_string['filter'])) ? Convert::raw2sql($query_string['filter']) : '';
            $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';
            $type  = (isset($query_string['type'])) ? Convert::raw2sql($query_string['type']) : '';

            list($list, $count) = $this->repository->getAllByPage
            (
                $page,
                $page_size,
                $order,
                $search_term,
                $type,
                $filter
            );

            $items    = [];
            foreach ($list as $item){
                $expires_epoch = new DateTime($item->Expires);
                $created_epoch = new DateTime($item->Created);
                $items[] =
                    [
                        'id'                => intval($item->ID),
                        'identifier'        => $item->Identifier,
                        'value'             => $item->Value,
                        'value_description' => $item->ValueDescription,
                        'created'           => $created_epoch->format("Y-m-d h:i:s A"),
                        'expires'           => $expires_epoch->format("Y-m-d h:i:s A"),
                        'member_id'         => intval($item->FoundationMember()->ID),
                        'member_full_name'  => $item->FoundationMember()->FullName,
                        'member_email'      => $item->FoundationMember()->Email,
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

    /**
     * @param SS_HTTPRequest $request
     */
    function exportMetricToCSV(SS_HTTPRequest $request){

        $query_string = $request->getVars();
        $order        = (isset($query_string['order'])) ? Convert::raw2sql($query_string['order']) : '';
        $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';
        $type         = (isset($query_string['type'])) ? Convert::raw2sql($query_string['type']) : '';
        $filter       = (isset($query_string['filter'])) ? Convert::raw2sql($query_string['filter']) : '';

        list($list, $count) = $this->repository->getAllByPage
        (
            1,
            PHP_INT_MAX,
            $order,
            $search_term,
            $type,
            $filter
        );

        $filename     = "AUCMetrics" . date('Ymd') . ".csv";

        $items    = [];
        foreach ($list as $item){
            $expires_epoch = new DateTime($item->Expires);
            $created_epoch = new DateTime($item->Created);
            $items[] =
                [
                    'id'                => intval($item->ID),
                    'identifier'        => $item->Identifier,
                    'value'             => $item->Value,
                    'value_description' => $item->ValueDescription,
                    'created'           => $created_epoch->format("Y-m-d h:i:s A"),
                    'expires'           => $expires_epoch->format("Y-m-d h:i:s A"),
                    'member_id'         => intval($item->FoundationMember()->ID),
                    'member_full_name'  => $item->FoundationMember()->FullName,
                    'member_email'      => $item->FoundationMember()->Email,
                ];
        }

        return CSVExporter::getInstance()->export($filename, $items, ',');
    }
}