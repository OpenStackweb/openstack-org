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
 * Defines the OpenStackDaysPage page type
 */
class OpenStackDaysPage extends Page {
    private static $db = array(
        'AboutDescription' => 'HTMLText',
        'HostIntro'        => 'HTMLText',
        'HostFAQs'         => 'HTMLText',
        'ToolkitDesc'      => 'HTMLText',
        'ArtworkIntro'     => 'HTMLText',
        'CollateralIntro'  => 'HTMLText',

    );

    private static $has_one = array();

    private static $has_many = array(
        'AboutVideos'        => 'OpenStackDaysVideo.About',
        'HeaderPics'         => 'OpenStackDaysImage.HeaderPics',
        'OfficialGuidelines' => 'OpenStackDaysDoc.OfficialGuidelines',
        'PlanningTools'      => 'OpenStackDaysDoc.PlanningTools',
        'Artwork'            => 'OpenStackDaysDoc.Artwork',
        'Collaterals'        => 'OpenStackDaysVideo.Collaterals',
        'Media'              => 'OpenStackDaysDoc.Media',
    );

    private static $many_many_extraFields = array();

    function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->insertBefore(new Tab('About'), 'Settings');
        $fields->insertBefore(new Tab('Host'), 'Settings');

        // header
        $fields->removeByName('Content');
        $fields->addFieldToTab(
            'Root.Main',
            $uploadField = new UploadField('HeaderPics','Header Pictures')
        );
        $uploadField->setAllowedMaxFileNumber(10);
        $uploadField->setFolderName('openstackdays');

        // About
        $fields->addFieldToTab(
            'Root.About',
            $about_desc = new HtmlEditorField('AboutDescription','Intro Text',$this->AboutDescription)
        );
        $config = new GridFieldConfig_RecordEditor(10);
        $config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
            array('YoutubeID' => 'YoutubeID', 'Caption' => 'Caption', 'Active' => 'Active')
        );
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.About',
            new GridField('AboutVideos', 'Videos', $this->AboutVideos(), $config)
        );

        // Host
        $fields->addFieldToTab(
            'Root.Host',
            $host_intro = new HtmlEditorField('HostIntro','Intro Text',$this->HostIntro)
        );

        $fields->addFieldToTab(
            'Root.Host',
            $host_faq = new HtmlEditorField('HostFAQs','FAQs',$this->HostFAQs)
        );

        $fields->addFieldToTab(
            'Root.Host',
            $toolkit_text = new HtmlEditorField('ToolkitDesc','Toolkit Text',$this->ToolkitDesc)
        );

        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Host',
            new GridField('OfficialGuidelines', 'Official Guidelines', $this->OfficialGuidelines(), $config)
        );

        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Host',
            new GridField('PlanningTools', 'Planning Tools', $this->PlanningTools(), $config)
        );

        $fields->addFieldToTab(
            'Root.Host',
            $artwork_intro = new HtmlEditorField('ArtworkIntro','Artwork intro text',$this->ArtworkIntro)
        );
        $artwork_intro->setRows(4);
        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Host',
            new GridField('Artwork', 'Artwork', $this->Artwork(), $config)
        );

        $fields->addFieldToTab(
            'Root.Host',
            $collateral_intro = new HtmlEditorField('CollateralIntro','Collateral intro text',$this->CollateralIntro)
        );
        $collateral_intro->setRows(4);

        $config = new GridFieldConfig_RecordEditor(10);
        $config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
            array('YoutubeID' => 'YoutubeID', 'Caption' => 'Caption', 'Active' => 'Active')
        );
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Host',
            new GridField('Collaterals', 'Video / Presentations / Collateral', $this->Collaterals(), $config)
        );

        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Host',
            new GridField('Media', 'PR / Media', $this->Media(), $config)
        );

        return $fields;
	}

}
/**
 * Class OpenStackDaysPage_Controller
 */
class OpenStackDaysPage_Controller extends Page_Controller {

    private $event_manager;

	private static $allowed_actions = array (
	);

	function init() {
	    parent::init();
        Requirements::javascript('themes/openstack/bower_assets/slick-carousel/slick/slick.min.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('events/js/openstackdays.js');
        Requirements::css('themes/openstack/bower_assets/slick-carousel/slick/slick.css');
        Requirements::css('software/css/software.css');
        Requirements::css('events/css/openstackdays.css');
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

    function FutureOpenstackDaysEvents($num) {
        $filter_array = array('EventEndDate:GreaterThanOrEqual'=> date('Y-m-d'));
        $filter_array['EventCategory'] = 'Openstack Days';
        $pulled_events = EventPage::get()->filter($filter_array)->sort(array('EventStartDate'=>'ASC','EventContinent'=>'ASC'))->limit($num);

        return $pulled_events;
    }

    function EventsYearlyCountText() {
        $this_year = date('Y');
        $event_count = EventPage::get()->where("YEAR(EventStartDate) = '".$this_year."' AND EventPage.EventCategory = 'Openstack Days'")
                                       ->sort(array('EventStartDate'=>'ASC','EventContinent'=>'ASC'))->count();


        return "There are more than <strong>".$event_count." OpenStack Days</strong> scheduled for ".$this_year;
    }

    public function getFeaturedEvents() {
        return FeaturedEvent::get('FeaturedEvent')
            ->leftJoin('EventPage','EventPage.ID = FeaturedEvent.EventID')
            ->sort('EventPage.EventStartDate','DESC');
    }

    public function getGroupedPlanningTools() {
        $grouped_array = array();
        foreach ($this->PlanningTools()->sort('SortOrder') as $key => $tool) {
            if ($tool->Group) {
                if (!isset($grouped_array[$tool->Group]))
                    $grouped_array[$tool->Group] = new ArrayData(array('Group' => $tool->Group,'First' => intval($key == 0),'Tools' => new ArrayList()));
                $grouped_array[$tool->Group]->Tools->push($tool);
            } else {
                $tool->First = intval($key == 0);
                $grouped_array[] = $tool;
            }
        }

        return new ArrayList($grouped_array);
    }

}
