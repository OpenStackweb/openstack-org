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
 * Defines the JobsHolder page type
 */
class EventHolder extends Page {
   private static$db = array(
       'OpenstackDaysContent'    => 'HTMLText',
       'OpenstackDaysVideoID1'   => 'Text',
       'OpenstackDaysVideoDesc1' => 'Text',
       'OpenstackDaysVideoID2'   => 'Text',
       'OpenstackDaysVideoDesc2' => 'Text',
       'OpenstackHackathonsContent'    => 'HTMLText',
       'OpenstackHackathonsVideoID1'   => 'Text',
       'OpenstackHackathonsVideoDesc1' => 'Text',
       'OpenstackHackathonsVideoID2'   => 'Text',
       'OpenstackHackathonsVideoDesc2' => 'Text',
   );

   private static $has_one = array(
   );

    /** static $icon = "icon/path"; */
    function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab("Root.OpenstackDays", new HtmlEditorField("OpenstackDaysContent", "Intro Text"));
        $fields->addFieldToTab("Root.OpenstackDays", new TextField("OpenstackDaysVideoID1", "Youtube ID 1"));
        $fields->addFieldToTab("Root.OpenstackDays", new TextField("OpenstackDaysVideoDesc1", "Video Description"));
        $fields->addFieldToTab("Root.OpenstackDays", new TextField("OpenstackDaysVideoID2", "Youtube ID 2"));
        $fields->addFieldToTab("Root.OpenstackDays", new TextField("OpenstackDaysVideoDesc2", "Video Description"));
        $fields->addFieldToTab("Root.OpenstackHackathons", new HtmlEditorField("OpenstackHackathonsContent", "Intro Text"));
        $fields->addFieldToTab("Root.OpenstackHackathons", new TextField("OpenstackHackathonsVideoID1", "Youtube ID 1"));
        $fields->addFieldToTab("Root.OpenstackHackathons", new TextField("OpenstackHackathonsVideoDesc1", "Video Description"));
        $fields->addFieldToTab("Root.OpenstackHackathons", new TextField("OpenstackHackathonsVideoID2", "Youtube ID 2"));
        $fields->addFieldToTab("Root.OpenstackHackathons", new TextField("OpenstackHackathonsVideoDesc2", "Video Description"));
		return $fields;
	}
      
}
/**
 * Class EventHolder_Controller
 */
class EventHolder_Controller extends Page_Controller {

    private $event_manager;

	private static $allowed_actions = array (
		'AjaxFutureEvents',
		'AjaxFutureSummits',
		'AjaxPastSummits',
        'openstackdays',
        'openstackhackathons'
	);

	function init() {
	    parent::init();

        Requirements::css('events/css/events.css');
        Requirements::css('events/css/events.list.css');
        Requirements::set_force_js_to_bottom(true);
		Requirements::javascript('events/js/events.js');
        $this->buildEventManager();
	}

    function buildEventManager() {
        $this->event_manager = new EventManager(
            $this->repository,
            new EventRegistrationRequestFactory,
            null,
            new SapphireEventPublishingService,
            new EventValidatorFactory,
            SapphireTransactionManager::getInstance()
        );
    }

	function RandomEventImage(){ 
		$image = Image::get()->filter(array('ClassName:not' => 'Folder'))->where("ParentID = (SELECT ID FROM File WHERE ClassName = 'Folder'
		AND Name = 'EventImages')")->sort('RAND()')->first();
		return $image;
	}
	
	function PastEvents($num = 4) {
		return EventPage::get()->filter(array('EventEndDate:LessThanOrEqual'=> date('Y-m-d') , 'IsSummit'=>1))->sort('EventEndDate')->limit($num);
	}

	function FutureEvents($num, $filter = '') {
        if ($this->event_manager == null) {
            $this->buildEventManager();
        }

        $filterLowerCase = strtolower($filter);
        $events_array = new ArrayList();
        if ($filterLowerCase != 'other') {
            $filter_array = array('EventEndDate:GreaterThanOrEqual'=> date('Y-m-d'));
            if (strtolower($filter) != 'all' && $filter != '') {
                $filter_array['EventCategory'] = $filter;
            }
            $pulled_events = EventPage::get()->filter($filter_array)->sort('EventStartDate','ASC')->limit($num)->toArray();
        }
        else {
            $pulled_events = EventPage::get()->where("EventCategory is null and EventEndDate >= CURDATE()")->sort('EventStartDate','ASC')->limit($num)->toArray();
        }

        $events_array->merge($pulled_events);

		return $events_array->sort('EventStartDate', 'ASC')->limit($num,0)->toArray();
	}

    function FutureOpenstackDaysEvents($num) {
        $filter_array = array('EventEndDate:GreaterThanOrEqual'=> date('Y-m-d'));
        $filter_array['EventCategory'] = 'Openstack Days';
        $pulled_events = EventPage::get()->filter($filter_array)->sort(array('EventStartDate'=>'ASC','EventContinent'=>'ASC'))->limit($num);

        return $pulled_events;
    }

    function FutureOpenstackHackathonsEvents($num) {
        $filter_array = array('EventEndDate:GreaterThanOrEqual'=> date('Y-m-d'));
        $filter_array['EventCategory'] = 'Openstack App Hackathon';
        $pulled_events = EventPage::get()->filter($filter_array)->sort(array('EventStartDate'=>'ASC','EventContinent'=>'ASC'))->limit($num);

        return $pulled_events;
    }

    function PastSummits($num) {
	    return EventPage::get()->filter(array('EventEndDate:LessThanOrEqual'=> date('Y-m-d') , 'IsSummit'=>1))->sort('EventEndDate','DESC')->limit($num);
    }

    function FutureSummits($num) {
	    return EventPage::get()->filter(array('EventEndDate:GreaterThanOrEqual'=> date('Y-m-d') , 'IsSummit'=>1))->sort('EventStartDate','ASC')->limit($num);
    }

    public function getEvents($num = 4, $type, $filter = '') {
        $output = '';

        switch ($type) {
            case 'future_events':
                $events = $this->FutureEvents($num,$filter);
                break;
            case 'future_summits':
                $events = $this->FutureSummits($num);
                break;
            case 'past_summits':
                $events = $this->PastSummits($num);
                break;
        }

        if ($events) {
            foreach ($events as $key => $event) {
                $first = ($key == 0);
                $data = array('IsEmpty'=>0,'IsFirst'=>$first);

                $output .= $event->renderWith('EventHolder_event', $data);
            }
        } else {
            $data = array('IsEmpty'=>1);
            $event = new EventPage();
            $output .= $event->renderWith('EventHolder_event', $data);
        }

        return $output;
    }

    function AjaxFutureEvents() {
        $filter = $_POST['filter'];
        $event_controller = new EventHolder_Controller();
        return $event_controller->getEvents(100,'future_events',$filter);
    }

    function AjaxFutureSummits() {
        return $this->getEvents(5,'future_summits');
    }

    function AjaxPastSummits() {
        return $this->getEvents(5,'past_summits');
    }

	function PostEventLink(){
		$page = EventRegistrationRequestPage::get()->first();
		if($page){
			return $page->getAbsoluteLiveLink(false);
		}
		return '#';
	}

    /**
     * @return string
     */
    function EventTypes() {
        $count_by_event_type= $this->event_manager->getCountByType();

        $selected_class = " event-type-selected";

        $event_types = array_keys($count_by_event_type);
        $event_types_count = array_values($count_by_event_type);

        $event_type_links = "";
        for($i = 0; $i < count($event_types_count); $i++) {

            $event_type_links .= sprintf('<a href="#" class="event-type-link%s" data-type="%s">%s (%s)</a>',
                $selected_class,
                $event_types[$i],
                $event_types[$i],
                $event_types_count[$i]
            );
            $selected_class = "";
        }

        return $event_type_links;
    }

    function getFeaturedEvent() {
        return EventPage::get()->filter(array('Featured'=> 1))->sort('EventStartDate','ASC')->limit(1);
    }

    function openstackdays() {
        Requirements::css('events/css/openstackdays.css');
        return $this->renderWith(array('EventHolder_openstackdays','EventHolder','Page'));
    }

    function openstackhackathons() {
        Requirements::css('events/css/openstackhackathons.css');
        return $this->renderWith(array('EventHolder_openstackhackathons','EventHolder','Page'));
    }
}
