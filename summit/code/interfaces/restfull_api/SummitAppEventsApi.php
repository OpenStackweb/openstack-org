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
class SummitAppEventsApi extends AbstractRestfulJsonApi {



    /**
     * @var IEntityRepository
     */
    private $summit_repository;

    /**
     * @var ISummitEventRepository
     */
    private $summitevent_repository;

    /**
     * @var ISummitPresentationRepository
     */
    private $summitpresentation_repository;

    public function __construct()
    {
        parent::__construct();
        // TODO: set by IOC
        $this->summit_repository             = new SapphireSummitRepository;
        $this->summitevent_repository        = new SapphireSummitEventRepository();
        $this->summitpresentation_repository = new SapphireSummitPresentationRepository();
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
        if(!Permission::check('ADMIN')) return false;
        return $this->checkOwnAjaxRequest();
    }

    protected function authenticate() {
        return true;
    }


    static $url_handlers = array(
        'GET unpublished/$Source!'  => 'getUnpublishedEventsBySource',
    );

    static $allowed_actions = array(
        'getUnpublishedEventsBySource',
    );

    public function getUnpublishedEventsBySource(SS_HTTPRequest $request) {
        try {
            $query_string = $request->getVars();
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $source        = strtolower(Convert::raw2sql($request->param('Source')));
            $track_list_id = isset($query_string['track_list_id']) ? Convert::raw2sql($query_string['track_list_id']) : null;
            $page          = isset($query_string['page']) ? intval($query_string['page']) : 1;
            $page_size     = isset($query_string['page_size']) ? intval($query_string['page_size']) : 10;
            $order         = isset($query_string['order']) ? Convert::raw2sql($query_string['order']) : null;

            switch ($source) {
                case 'tracks': {
                    list($page, $page_size, $count, $data) = $this->summitpresentation_repository->getUnpublishedBySummitAndTrackList($summit_id, $track_list_id, $page,$page_size, $order);
                }
                break;
                case 'presentations': {
                    list($page, $page_size, $count, $data) = $this->summitpresentation_repository->getUnpublishedBySummit($summit_id,  $page,$page_size, $order);
                }
                    break;
                case 'events': {

                }
                break;
            }

            $events = array();
            foreach ($data as $e)
            {
                $entry = array
                (
                    'id'          => $e->ID,
                    'title'       => $e->Title,
                    'description' => $e->Description,
                    'type_id'     => $e->TypeID,
                );

                if ($e instanceof Presentation)
                {
                    $speakers = array();
                    foreach ($e->Speakers() as $s) {
                        array_push($speakers, $s->ID);
                    }

                    $entry['speakers_id']  = $speakers;
                    $entry['moderator_id'] = $e->ModeratorID;
                    $entry['track_id']     = $e->CategoryID;
                    $entry['level']        = $e->Level;
                }
                array_push($events, $entry);
            }

            return $this->ok(
                array
                (
                    'data'        => $events,
                    'page'        => $page,
                    'page_size'   => $page_size,
                    'total_pages' => ceil($count/$page_size)
                )
            );
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }
}