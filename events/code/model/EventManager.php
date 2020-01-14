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
final class EventManager implements IEventManager {

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
     * @var IExternalEventsApi|null
     */
	private $external_event_api;

	/**
	 * @param IEntityRepository                $event_repository
	 * @param IEventRegistrationRequestFactory $factory
	 * @param IGeoCodingService                $geo_coding_service
	 * @param IEventPublishingService          $event_publishing_service
	 * @param IEventValidatorFactory           $validator_factory
     * @param IExternalEventsApi               $external_event_api
	 * @param ITransactionManager              $tx_manager
	 */
	public function __construct(?IEntityRepository $event_repository,
	                            ?IEventRegistrationRequestFactory $factory,
								?IGeoCodingService $geo_coding_service,
								?IEventPublishingService $event_publishing_service,
								?IEventValidatorFactory $validator_factory,
                                ?IExternalEventsApi $external_event_api,
	                            ITransactionManager $tx_manager){

		$this->event_repository            = $event_repository;
		$this->tx_manager                  = $tx_manager;
		$this->factory                     = $factory;
		$this->geo_coding_service          = $geo_coding_service;
		$this->event_publishing_service    = $event_publishing_service;
		$this->validator_factory           = $validator_factory;
		$this->external_event_api          = $external_event_api;
	}

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
            $event->registerLocation($data['location'],$data['continent']);
            $event->registerDuration($factory->buildEventDuration($data));
            $event->registerLogoUrl($data['logo_url']);
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
            $event->registerLocation($data['location'],$data['continent']);
            $event->registerDuration($factory->buildEventDuration($data));
            $event->registerLogoUrl($data['logo_url']);

            $repository->add($event);

            return $event;
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

    public function deleteFeaturedEvent($id){
        $this->tx_manager->transaction(function() use ($id){
            FeaturedEvent::delete_by_id('FeaturedEvent',$id);
        });

    }

    /**
     * @return EventTypeSummary
     */
    public function getCountByType() {

        $count_by_event_type = array();

        $result = DB::query("
            SELECT EventCategory, COUNT(*) AS EventCategoryCount FROM EventPage where EventEndDate >= CURDATE() group by EventCategory
            order by (CASE WHEN EventCategory IS NULL then 1 ELSE 0 END), EventCategory
          ");


        $count_by_event_type = array("All" => 0);
        $all_count = 0;
        $event_type = new stdClass;
        foreach($result as $record) {
            $event_category = $record["EventCategory"] != null ? $record["EventCategory"] : "Other";
            $count_by_event_type[$event_category] = $record["EventCategoryCount"];
            $all_count += $record["EventCategoryCount"];
        }

        $count_by_event_type["All"] = $all_count;
        return $count_by_event_type;
    }

    /**
     * @param int $pageSize
     * @return array
     */
    function rssEvents(int $pageSize = 20):array
    {
        try {
            return $this->external_event_api->getAllUpcomingEvents($pageSize);
        }
        catch(Exception $ex){
            SS_Log::log("It wasn't possible to get rss content from source. Isolated occurrences of this error can be ignored since temporary glitches accessing url could be the cause. Information will be ingested on next run if that's the case",SS_Log::ERR);
            echo $ex->getMessage();
            return [];
        }
    }

    /**
     * @param $rss_events
     * @return ArrayList
     */
    function rss2events($rss_events) {
        $events_array = new ArrayList();
        foreach ($rss_events as $item) {
            $event_main_info = new EventMainInfo(html_entity_decode($item['name']),$item['link'],'Details','Meetups');
            $event_start_date = DateTime::createFromFormat('Y-m-d H:i', $item['local_date'].' '.$item['local_time']);
            $event_end_date = DateTime::createFromFormat('Y-m-d H:i', $item['local_date'].' '.$item['local_time']);
            // default is 3 hours
            $duration = isset($item['duration']) ? $item['duration']/1000: 3600 * 3;
            $event_end_date = $event_end_date->add(new DateInterval(sprintf('PT%sS',$duration)));
            $event_duration = new EventDuration($event_start_date, $event_end_date);
            $continent = $this->getContinentFromLocation($item['venue']['country']);

            $event = new EventPage();
            $event->registerMainInfo($event_main_info);
            $event->registerDuration($event_duration);
            if(isset($item['venue'])) {
                $venue = $item['venue'];
                $location = '';
                if(isset($venue['address_1'])) {
                    if (!empty($location)) $location .= ', ';
                    $location .= $venue['address_1'];
                }
                if(isset($venue['city'])) {
                    if(!empty($location)) $location .= ', ';
                    $location .= $venue['city'];
                }
                if(isset($venue['state'])){
                    if(!empty($location)) $location .= ', ';
                    $location .= $venue['state'];
                }
                if(isset($venue['localized_country_name'])) {
                    if (!empty($location)) $location .= ', ';
                    $location .= $venue['localized_country_name'];
                }
                $event->registerLocation($location, $continent);
            }
            $event->ExternalSourceId = $item['id'];
            $events_array->push($event);
        }

        return $events_array;
    }

    function saveRssEvents($events_array) {
        foreach ($events_array as $event) {

            $filter_array = [];
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

    /**
     * @return array
     */
    function getAllTypes() {
        $categories = EventPage::$event_categories;
        $event_types = DB::query("SELECT DISTINCT EventCategory FROM EventPage where EventCategory is not null ORDER BY EventCategory");
        foreach ($event_types as $event_type) {
            if (!in_array($event_type["EventCategory"], $categories)){
                $categories[] = $event_type["EventCategory"];
            }
        }
        return $categories;
    }

    /**
     * @param string $location
     * @return string
     */
    function getContinentFromLocation($location) {
        $loc_array = explode(',',$location);
        $country_code = (count($loc_array)) ? trim(end($loc_array)) : '';
        if ($country_code) {
            $sqlQuery = new SQLQuery();
            $sqlQuery->setFrom("Continent");
            $sqlQuery->selectField("Name");
            $sqlQuery->addLeftJoin("Continent_Countries","Continent_Countries.ContinentID = Continent.ID");
            $sqlQuery->addWhere("Continent_Countries.CountryCode = '$country_code'");
            $continent = $sqlQuery->execute()->first();
            if ($continent) {
                return $continent['Name'];
            }
        }
        return '';
    }

    /**
     * @param ArrayList $events_array
     */
    public function purgeRssEvents(ArrayList $events_array):void{
        $this->tx_manager->transaction(function() use($events_array){
            $events_to_purge = $this->event_repository->getRssForPurge($events_array);

            foreach($events_to_purge as $event) {
                $this->deleteEvent($event->ID);
            }
        });
    }

} 