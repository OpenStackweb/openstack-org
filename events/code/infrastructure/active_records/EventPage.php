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
 * Class EventPage
 */
class EventPage
    extends DataObject
    implements IEvent {

    private static $db = array(
        'Title'                => 'Varchar(255)',
        'EventStartDate'       => 'Date',
        'EventEndDate'         => 'Date',
        'EventLink'            => 'Text',
        'EventLinkLabel'       => 'Text',
        'EventCategory'        => 'Text',
        'EventLocation'        => 'Text',
        'EventSponsor'         => 'Text',
        'EventSponsorLogoUrl'  => 'Text',
        'IsSummit'             => 'Boolean',
        'ExternalSourceId'     => 'Varchar(255)',
        'EventContinent'       => 'Varchar(255)',
    );

    private static $defaults = array(
        'ExternalSourceId' => null,
        'EventContinent' => '',
    );

    private static $has_one = array();


    function getCMSFields() {
        $fields = parent::getCMSFields();

        // the date field is added in a bit more complex manner so it can have the dropdown date picker
        $EventStartDate = new DateField('EventStartDate','First Day of Event');
        $EventStartDate->setConfig('showcalendar', true);
        $EventStartDate->setConfig('showdropdown', true);
        $fields->addFieldToTab('Root.Main', $EventStartDate, 'Content');

        // same things for the event end date
        $EventEndDate = new DateField('EventEndDate','Last Day of Event');
        $EventEndDate->setConfig('showcalendar', true);
        $EventEndDate->setConfig('showdropdown', true);
        $fields->addFieldToTab('Root.Main', $EventEndDate, 'Content');

        $fields->addFieldToTab('Root.Main', new TextField('EventLink','Event Button Link (URL)'), 'Content');
        $fields->addFieldToTab('Root.Main', new TextField('EventLinkLabel','Event Button Label'), 'Content');

        $fields->addFieldToTab('Root.Main', new TextField('EventLocation','Event Location'), 'Content');
        $fields->addFieldToTab('Root.Main', new TextField('EventSponsor','Event Sponsor'), 'Content');
        $fields->addFieldToTab('Root.Main', new TextField('EventSponsorLogoUrl','URL of the Event Sponsor Logo'), 'Content');

        $fields->addFieldToTab('Root.Main', new CheckboxField ('IsSummit','Official OpenStack Summit Event'), 'Content');

        // remove unneeded fields
        $fields->removeFieldFromTab("Root.Main","MenuTitle");

        // rename fields
        $fields->renameField("Content", "Event Page Content");
        $fields->renameField("Title", "Event Title");

        return $fields;
    }

    public function formatDateRange() {
        $startDateArray = date_parse($this->EventStartDate);
        $endDateArray = date_parse($this->EventEndDate);

        if ($startDateArray["year"] == $endDateArray["year"]
            && $startDateArray["month"] == $endDateArray["month"]
            && $startDateArray["day"] == $endDateArray["day"]) {
            // single day range
            return date('M d, Y',strtotime($this->EventStartDate));
        } else if ($startDateArray["year"] == $endDateArray["year"]
            && $startDateArray["month"] == $endDateArray["month"]) {
            // multi-day, single month range
            $value = date('M d - ',strtotime($this->EventStartDate));
            $value .= date('d, Y',strtotime($this->EventEndDate));
            return $value;
        } else if ($startDateArray["year"] == $endDateArray["year"]) {
            // same year, spanning months (there days as well)
            $value = date('M d - ',strtotime($this->EventStartDate));
            $value .= date('M d, Y',strtotime($this->EventEndDate));
            return $value;
        } else {
            // must be different years (therefore months and days as well)
            $value = date('M d, Y - ',strtotime($this->EventStartDate));
            $value .= date('M d, Y',strtotime($this->EventEndDate));
            return $value;
        }
    }

    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function toggleSummit() {
        $this->IsSummit = !$this->IsSummit;
    }

    /**
     * @return EventMainInfo
     */
    function getMainInfo()
    {
        return new EventMainInfo($this->Title, $this->EventLink,$this->EventLinkLabel, $this->EventCategory);
    }

    /**
     * @return EventLocation
     */
    public function getLocation()
    {
        return $this->EventLocation;
    }

    /**
     * @return string
     */
    public function getContinent()
    {
        return $this->EventContinent;
    }

    /**
     * @return EventDuration
     */
    public function getDuration()
    {
        return new EventDuration(new DateTime($this->EventStartDate), new DateTime($this->EventEndDate));
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->EventSponsorLogoUrl;
    }

    /**
     * @param EventMainInfo $info
     * @return void
     */
    function registerMainInfo(EventMainInfo $info)
    {
        $this->Title = $info->getTitle();
        $this->EventLink = $info->getUrl();
        $this->EventLinkLabel = $info->getLabel();
        $this->EventCategory = $info->getCategory();
    }

    /**
     * @param string $location
     * @return void
     */
    function registerLocation($location, $continent) {
        $this->EventLocation = $location;
        $this->EventContinent = $continent;
    }

    /**
     * @param EventDuration $duration
     * @return void
     */
    public function registerDuration(EventDuration $duration)
    {
        $this->EventStartDate = $duration->getStartDate()->format('Y-m-d');
        $this->EventEndDate   = $duration->getEndDate()->format('Y-m-d ');
    }

    /**
     * @param $logo_url
     * @return void
     */
    public function registerLogoUrl($logo_url)
    {
        $this->EventSponsorLogoUrl = $logo_url;
    }

}
