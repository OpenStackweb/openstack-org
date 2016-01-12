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

    private $summit_service;

    public function __construct()
    {
        parent::__construct();
        // TODO: set by IOC
        $this->summit_repository             = new SapphireSummitRepository;
        $this->summitevent_repository        = new SapphireSummitEventRepository();
        $this->summitpresentation_repository = new SapphireSummitPresentationRepository();
        $this->summit_service                = new SummitService
        (
            $this->summit_repository,
            $this->summitevent_repository,
            SapphireTransactionManager::getInstance()
        );
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
        'GET unpublished/$Source!' => 'getUnpublishedEventsBySource',
        'PUT $EVENT_ID!/publish'   => 'publishEvent',
        'DELETE $EVENT_ID!/unpublish'    => 'unpublishEvent',
    );

    static $allowed_actions = array(
        'getUnpublishedEventsBySource',
        'publishEvent',
        'unpublishEvent',
    );

    public function getUnpublishedEventsBySource(SS_HTTPRequest $request) {
        try {
            $query_string = $request->getVars();
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $source        = strtolower(Convert::raw2sql($request->param('Source')));
            $valid_sources = array('tracks', 'presentations', 'events');

            if(!in_array($source, $valid_sources)) return $this->validationError(array('invalid requested source'));

            $track_list_id = isset($query_string['track_list_id']) ? Convert::raw2sql($query_string['track_list_id']) : null;
            $page          = isset($query_string['page']) ? intval($query_string['page']) : 1;
            $page_size     = isset($query_string['page_size']) ? intval($query_string['page_size']) : 10;
            $order         = isset($query_string['order']) ? Convert::raw2sql($query_string['order']) : null;
            $expand        = isset($query_string['expand']) ? Convert::raw2sql($query_string['expand']) : null;

            switch ($source)
            {
                case 'tracks':
                {
                    list($page, $page_size, $count, $data) = $this->summitpresentation_repository->getUnpublishedBySummitAndTrackList($summit_id, $track_list_id, $page,$page_size, $order);
                }
                break;
                case 'presentations':
                {
                    list($page, $page_size, $count, $data) = $this->summitpresentation_repository->getUnpublishedBySummit($summit_id,  $page,$page_size, $order);
                }
                break;
                case 'events':
                {
                    list($page, $page_size, $count, $data) = $this->summitevent_repository->getUnpublishedBySummit($summit_id,  $page,$page_size, $order);
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
                    if(!empty($expand) && strstr($expand, 'speakers')!== false)
                    {
                        foreach ($e->Speakers() as $s) {
                            array_push($speakers, array('id' => $s->ID, 'name' => $s->getName()));
                        }
                        $entry['speakers'] = $speakers;
                    }
                    else {
                        foreach ($e->Speakers() as $s) {
                            array_push($speakers, $s->ID);
                        }
                        $entry['speakers_id'] = $speakers;
                    }
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

    public function publishEvent(SS_HTTPRequest $request)
    {
        try
        {
           if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
           $query_string = $request->getVars();
           $summit_id    = intval($request->param('SUMMIT_ID'));
           $event_id     = intval($request->param('EVENT_ID'));
           $event_data   = $this->getJsonRequest();
           $summit = $this->summit_repository->getById($summit_id);
           if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
           $this->summit_service->publishEvent($summit, $event_data);
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
            return $this->notFound($ex2->getMessages());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function unpublishEvent(SS_HTTPRequest $request){

        try
        {
            if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
            $query_string = $request->getVars();
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $event_id     = intval($request->param('EVENT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $event        = $this->summitevent_repository->getById($event_id) ;
            if(is_null($event)) throw new NotFoundEntityException('SummitEvent', sprintf(' id %s', $event_id));
            $this->summit_service->unpublishEvent($summit, $event);
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
}