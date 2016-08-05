<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class EventCrudApi
 */
final class EventCrudApi
    extends AbstractRestfulJsonApi
{

    const ApiPrefix = 'api/v1/events';

    protected function isApiCall()
    {
        $request = $this->getRequest();
        if (is_null($request)) return false;
        return strpos(strtolower($request->getURL()), self::ApiPrefix) !== false;
    }

    /**
     * @var EventManager
     */
    private $event_manager;
    /**
     * @var IEventRepository
     */
    private $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new SapphireEventRepository;

        $this->event_manager = new EventManager(
            $this->repository,
            new EventRegistrationRequestFactory,
            new GoogleGeoCodingService
            (
                new SapphireGeoCodingQueryRepository,
                new UtilFactory,
                SapphireTransactionManager::getInstance()
            ),
            new SapphireEventPublishingService,
            new EventValidatorFactory,
            SapphireTransactionManager::getInstance()
        );
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return Permission::check("SANGRIA_ACCESS");
    }

    /**
     * @var array
     */
    static $url_handlers = array(
        'PUT $EVENT_ID/toggle_summit'   => 'toggleSummit',
        'PUT $EVENT_ID/delete'          => 'deleteEvent',
        'PUT $EVENT_ID/delete_featured' => 'deleteFeaturedEvent',
        'GET featured'                  => 'getFeaturedEvents',
        'GET $EVENT_ID'                 => 'getEvent',
        'PUT '                          => 'updateEvent',
        'POST '                         => 'addEvent',
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'toggleSummit',
        'deleteEvent',
        'deleteFeaturedEvent',
        'getEvent',
        'updateEvent',
        'addEvent',
        'getFeaturedEvents',
    );

    /**
     * @return SS_HTTPResponse
     */
    public function toggleSummit()
    {
        try {
            $event_id = (int)$this->request->param('EVENT_ID');
            $this->event_manager->toggleSummitEvent($event_id);
            return $this->updated();
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::ERR);
            return $this->notFound($ex1->getMessage());
        } catch (EntityValidationException $ex2) {
            SS_Log::log($ex2, SS_Log::NOTICE);
            return $this->validationError($ex2->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getEvent()
    {
        $event_id = (int)$this->request->param('EVENT_ID');
        try {
            $event = $this->repository->getById($event_id);
            if (!$event) throw new NotFoundEntityException('EventPage', sprintf('id %s', $event_id));
            return $this->ok(EventsAssembler::convertEventToArray($event));
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function addEvent()
    {
        try {
            $data = $this->getJsonRequest();
            if (!$data) return $this->serverError();
            $event = $this->event_manager->addEvent($data);
            return $event->ID;
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        } catch (EntityValidationException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function updateEvent()
    {
        try {
            $data = $this->getJsonRequest();
            if (!$data) return $this->serverError();
            $this->event_manager->updateEvent($data);
            return $this->updated();
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        } catch (EntityValidationException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function deleteEvent()
    {
        try {
            $event_id = (int)$this->request->param('EVENT_ID');
            $this->event_manager->deleteEvent($event_id);
            return $this->updated();
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        } catch (EntityValidationException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function deleteFeaturedEvent()
    {
        try {
            $event_id = (int)$this->request->param('EVENT_ID');
            $this->event_manager->deleteFeaturedEvent($event_id);
            return $this->updated();
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        } catch (EntityValidationException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getFeaturedEvents()
    {
        $offset = $this->request->getVar('offset');
        try {
            $featured_events = FeaturedEvent::get('FeaturedEvent')
                ->leftJoin('EventPage','EventPage.ID = FeaturedEvent.EventID')
                ->sort('EventPage.EventStartDate');

            $featured_result = array();
            foreach ($featured_events->limit(8,$offset) as $featured) {
                $featured_result[] = array(
                    'title' => $featured->Event()->Title,
                    'location' => $featured->Event()->Location,
                    'date' => $featured->Event()->formatDateRange(),
                    'image' => $featured->Picture()->CroppedImage(200,100)->getTag(),
                );
            }

            return $this->ok($featured_result);
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

}