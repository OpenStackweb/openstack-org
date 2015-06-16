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
 * Class EventManager
 */
final class EventManager {

	/**
	 * @var IEntityRepository
	 */
	private $event_repository;
	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;
	/**
	 * @var IEventRegistrationRequestFactory
	 */
	private $factory;
	/**
	 * @var IGeoCodingService
	 */
	private $geo_coding_service;

	/**
	 * @var IEventPublishingService
	 */
	private $event_publishing_service;

	/**
	 * @var IEventValidatorFactory
	 */
	private $validator_factory;

	/**
	 * @param IEntityRepository                $event_repository
	 * @param IEventRegistrationRequestFactory $factory
	 * @param IGeoCodingService                $geo_coding_service
	 * @param IEventPublishingService          $event_publishing_service
	 * @param IEventValidatorFactory           $validator_factory
	 * @param ITransactionManager              $tx_manager
	 */
	public function __construct(IEntityRepository $event_repository,
	                            IEventRegistrationRequestFactory $factory,
								IGeoCodingService $geo_coding_service,
								IEventPublishingService $event_publishing_service,
								IEventValidatorFactory $validator_factory,
	                            ITransactionManager $tx_manager){
		$this->event_repository                      = $event_repository;
		$this->tx_manager                            = $tx_manager;
		$this->factory                               = $factory;
		$this->geo_coding_service                    = $geo_coding_service;
		$this->event_publishing_service              = $event_publishing_service;
		$this->validator_factory                     = $validator_factory;
	}

    /**
     * @param $id.event-type-link {

}
     * @return IEvent
     */
    public function toggleSummitEvent($id){
        $event_repository          = $this->event_repository;

        $event =  $this->tx_manager->transaction(function() use ($id, $event_repository){
            $event = $event_repository->getById($id);
            if(!$event) throw new NotFoundEntityException('EventPage',sprintf('id %s',$id ));
            $event->toggleSummit();

            return $event;
        });

        return $event;
    }

    /**
     * @param array $data
     * @return IEvent
     */
	public function updateEvent(array $data){

		$this_var           = $this;
		$validator_factory  = $this->validator_factory;
		$repository         = $this->event_repository;
		$factory            = $this->factory;

		return  $this->tx_manager->transaction(function() use ($this_var,$factory, $validator_factory, $data, $repository){
			$validator = $validator_factory->buildValidatorForEvent($data);
			if ($validator->fails()) {
				throw new EntityValidationException($validator->messages());
			}
			$event = $repository->getById(intval($data['id']));
			if(!$event)
				throw new NotFoundEntityException('EventPage',sprintf('id %s',$data['id'] ));

            $event->registerMainInfo($factory->buildEventMainInfo($data));
            $event->registerLocation($data['location']);
            $event->registerDuration($factory->buildEventDuration($data));
		});
	}

    /**
     * @param array $data
     * @return IEvent
     */
    public function addEvent(array $data){

        $this_var           = $this;
        $validator_factory  = $this->validator_factory;
        $repository         = $this->event_repository;
        $factory            = $this->factory;

        return  $this->tx_manager->transaction(function() use ($this_var,$factory, $validator_factory, $data, $repository){
            $validator = $validator_factory->buildValidatorForEvent($data);
            if ($validator->fails()) {
                throw new EntityValidationException($validator->messages());
            }

            $event = new EventPage();

            $event->registerMainInfo($factory->buildEventMainInfo($data));
            $event->registerLocation($data['location']);
            $event->registerDuration($factory->buildEventDuration($data));

            $event_id = $repository->add($event);

            return $event_id;
        });
    }

    /**
     * @param $id
     * @return IEvent
     */
    public function deleteEvent($id){
        $event_repository = $this->event_repository;

        $event =  $this->tx_manager->transaction(function() use ($id, $event_repository){
            $event = $event_repository->getById($id);
            $event_repository->delete($event);

        });

    }

    /**
     * @return EventTypeSummary
     */
    public function getCountByType() {
        $countByEventType = new stdclass;

        $filter_array = array('EventEndDate:GreaterThanOrEqual'=> date('Y-m-d'));
        $countByEventType->all = EventPage::get()->filter($filter_array)->Count();

        $filter_array['EventCategory'] = 'Meetups';
        $countByEventType->meetup = EventPage::get()->filter($filter_array)->Count();

        $filter_array['EventCategory'] = 'Industry';
        $countByEventType->industry = EventPage::get()->filter($filter_array)->Count();

        $filter_array['EventCategory'] = 'OpenStack Days';
        $countByEventType->openStackDays = EventPage::get()->filter($filter_array)->Count();

        $countByEventType->other = EventPage::get()->where("EventCategory is null and EventEndDate >= CURDATE()")->Count();

        return $countByEventType;
    }

    /**
     * @param int $limit
     * @return ArrayList
     */
    function rssEvents($limit = 7)
    {
        $feed = new RestfulService('https://groups.openstack.org/events-upcoming.xml', 7200);

        $feedXML = $feed->request()->getBody();

        // Extract items from feed
        $result = $feed->getValues($feedXML, 'channel', 'item');

        foreach ($result as $item) {
            $item->pubDate = date("D, M jS Y", strtotime($item->pubDate));
            $DOM = new DOMDocument;
            $DOM->loadHTML(html_entity_decode($item->description));
            $span_tags = $DOM->getElementsByTagName('span');
            foreach ($span_tags as $tag) {
                if ($tag->getAttribute('property') == 'schema:startDate') {
                    $item->startDate = $tag->getAttribute('content');
                } else if ($tag->getAttribute('property') == 'schema:endDate') {
                    $item->endDate = $tag->getAttribute('content');
                }
            }
            $div_tags = $DOM->getElementsByTagName('div');
            foreach ($div_tags as $tag) {
                if ($tag->getAttribute('property') == 'schema:location') {
                    $item->location = $tag->nodeValue;
                }
            }
        }

        return $result->limit($limit, 0);
    }

    /**
     * @param array $rss_events
     */
    function rss2events($rss_events) {
        $events_array = new ArrayList();
        foreach ($rss_events as $item) {
            $event_main_info = new EventMainInfo(html_entity_decode($item->title),$item->link,'Details','Meetups');
            $event_start_date = DateTime::createFromFormat(DateTime::ISO8601, $item->startDate);
            $event_end_date = DateTime::createFromFormat(DateTime::ISO8601, $item->endDate);
            $event_duration = new EventDuration($event_start_date,$event_end_date);
            $event = new EventPage();
            $event->registerMainInfo($event_main_info);
            $event->registerDuration($event_duration);
            $event->registerLocation($item->location);
            $event->ExternalSourceId = explode(' ', $item->guid)[0];
            $events_array->push($event);
        }

        return $events_array;
    }

    function saveRssEvents($events_array) {
        foreach ($events_array as $event) {

            $filter_array = array();
            $filter_array["EventEndDate"] = $event->EventEndDate;
            $filter_array["ExternalSourceId"] = $event->ExternalSourceId;

            $count = EventPage::get()->filter($filter_array)->Count();

            $event_repository = $this->event_repository;

            if ($count == 0) {
                $this->tx_manager->transaction(function() use ($event_repository, $event){
                    $event_repository->add($event);
                });
            }
        }
    }
} 