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
final class Summit extends DataObject implements ISummit
{

    private static $db = array
    (
        'Name'                        => 'Varchar(255)',
        'Title'                       => 'Varchar',
        'SummitBeginDate'             => 'SS_Datetime',
        'SummitEndDate'               => 'SS_Datetime',
        'SubmissionBeginDate'         => 'SS_Datetime',
        'SubmissionEndDate'           => 'SS_Datetime',
        'VotingBeginDate'             => 'SS_Datetime',
        'VotingEndDate'               => 'SS_Datetime',
        'SelectionBeginDate'          => 'SS_Datetime',
        'SelectionEndDate'            => 'SS_Datetime',
        'RegistrationBeginDate'       => 'SS_Datetime',
        'RegistrationEndDate'         => 'SS_Datetime',
        'Active'                      => 'Boolean',
        'DateLabel'                   => 'Varchar',
        'Link'                        => 'Varchar',
        'RegistrationLink'            => 'Text',
        'ComingSoonBtnText'           => 'Text',
        // https://www.eventbrite.com
        'ExternalEventId'             => 'Text',
        'TimeZone'                    => 'Text',
    );


    public function setSummitBeginDate($value)
    {
        if(!empty($value))
        {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField('SummitBeginDate', $value);
        }
    }

    public function getSummitBeginDate()
    {
        $value = $this->getField('SummitBeginDate');
        return $this->convertDateFromUTC2TimeZone($value);
    }

    public function setSummitEndDate($value)
    {
        if(!empty($value))
        {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField('SummitEndDate', $value);
        }
    }

    public function getSummitEndDate()
    {
        $value = $this->getField('SummitEndDate');
        return $this->convertDateFromUTC2TimeZone($value);
    }

    public function setSubmissionBeginDate($value)
    {
        if(!empty($value))
        {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField('SubmissionBeginDate', $value);
        }
    }

    public function getSubmissionBeginDate()
    {
        $value = $this->getField('SubmissionBeginDate');
        return $this->convertDateFromUTC2TimeZone($value);
    }

    public function setSubmissionEndDate($value)
    {
        if(!empty($value))
        {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField('SubmissionEndDate', $value);
        }
    }

    public function getSubmissionEndDate()
    {
        $value = $this->getField('SubmissionEndDate');
        return $this->convertDateFromUTC2TimeZone($value);
    }

    public function setVotingBeginDate($value)
    {
        if(!empty($value))
        {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField('VotingBeginDate', $value);
        }
    }

    public function getVotingBeginDate()
    {
        $value = $this->getField('VotingBeginDate');
        return $this->convertDateFromUTC2TimeZone($value);
    }

    public function setVotingEndDate($value)
    {
        if(!empty($value))
        {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField('VotingEndDate', $value);
        }
    }

    public function getVotingEndDate()
    {
        $value = $this->getField('VotingEndDate');
        return $this->convertDateFromUTC2TimeZone($value);
    }

    public function setSelectionBeginDate($value)
    {
        if(!empty($value))
        {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField('SelectionBeginDate', $value);
        }
    }

    public function getSelectionBeginDate()
    {
        $value = $this->getField('SelectionBeginDate');
        return $this->convertDateFromUTC2TimeZone($value);
    }

    public function setSelectionEndDate($value)
    {
        if(!empty($value))
        {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField('SelectionEndDate', $value);
        }
    }

    public function getSelectionEndDate()
    {
        $value = $this->getField('SelectionEndDate');
        return $this->convertDateFromUTC2TimeZone($value);
    }

    public function setRegistrationBeginDate($value)
    {
        if(!empty($value))
        {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField('RegistrationBeginDate', $value);
        }
    }

    public function getRegistrationBeginDate()
    {
        $value = $this->getField('RegistrationBeginDate');
        return $this->convertDateFromUTC2TimeZone($value);
    }

    public function setRegistrationEndDate($value)
    {
        if(!empty($value))
        {
            $value = $this->convertDateFromTimeZone2UTC($value);
            $this->setField('RegistrationEndDate', $value);
        }
    }

    public function getRegistrationEndDate()
    {
        $value = $this->getField('RegistrationEndDate');
        return $this->convertDateFromUTC2TimeZone($value);
    }

    private static $has_one = array
    (
        'Logo' => 'BetterImage',
    );

    private static $has_many = array
    (
        'Presentations'                => 'Presentation',
        'Categories'                   => 'PresentationCategory',
        'Locations'                    => 'SummitAbstractLocation',
        'Types'                        => 'SummitType',
        'EventTypes'                   => 'SummitEventType',
        'Events'                       => 'SummitEvent',
        'Attendees'                    => 'SummitAttendee',
        'SummitTicketTypes'            => 'SummitTicketType',
        'SummitRegistrationPromoCodes' => 'SummitRegistrationPromoCode',
        'Notifications'                => 'SummitPushNotification',
    );

    private static $summary_fields = array
    (
        'Title'  => 'Title',
        'Status' => 'Status',
    );

    private static $searchable_fields = array
    (
    );

    public static function get_active()
    {
        $summit = Summit::get()->filter
        (
            array
            (
                'Active' => true
            )
        )->first();

        return $summit ?: Summit::create();
    }

    public function checkRange($key)
    {
        $beginField = "{$key}BeginDate";
        $endField   = "{$key}EndDate";

        if (!$this->hasField($beginField) || !$this->hasField($endField)) return false;

        return (time() > $this->obj($beginField)->format('U')) && (time() < $this->obj($endField)->format('U'));
    }


    public function getStatus()
    {
        if (!$this->Active) return "INACTIVE";

        if ($this->checkRange("Submission")) return "ACCEPTING SUBMISSIONS";
        if ($this->checkRange("Voting")) return "COMMUNITY VOTING";
        if ($this->checkRange("Selection")) return "TRACK CHAIR SELECTION";
        if ($this->checkRange("Summit")) return "SUMMIT IS ON";

        return "DRAFT";
    }

    public function getNext()
    {
        $end_date = $this->getField('SummitEndDate');

        return Summit::get()->filter( array
        (
            'SummitEndDate:GreaterThan' => $end_date,
            'Active' => 1,
        ))->sort('SummitEndDate', 'ASC')->first();
    }


    public function getTitle(){
        $title = $this->getField('Title');
        $name  = $this->getField('Name');
        return empty($title)? $name : $title;
    }

    function TalksByMemberID($memberID)
    {

        $SpeakerList = new ArrayList();

        // Pull any talks that belong to this Summit and are owned by member
        $talksMemberOwns = $this->Talks("`OwnerID` = " . $memberID . " AND `SummitID` = " . $this->ID);
        $SpeakerList->merge($talksMemberOwns);

        // Now pull any talks that belong to this Summit and the member is listed as a speaker
        $speaker = Speaker::get()->filter('memberID', $memberID)->first();
        if ($speaker) {
            $talksMemberIsASpeaker = $speaker->TalksBySummitID($this->ID);

            // Now merge and de-dupe the lists
            $SpeakerList->merge($talksMemberIsASpeaker);
            $SpeakerList->removeDuplicates('ID');
        }

        return $SpeakerList;
    }

    /*
     * @return int
     */
    public static function CurrentSummitID()
    {
        $current = self::CurrentSummit();
        return is_null($current) ? 0 : $current->ID;
    }


    public static function ActiveSummit()
    {
        $summit = self::CurrentSummit();
        if(is_null($summit)) $summit = self::GetUpcoming();
        return $summit;
    }

    public static function ActiveSummitID()
    {
        $current = self::ActiveSummit();
        return is_null($current) ? 0 : $current->ID;
    }

    /**
     * @return ISummit
     */
    public static function CurrentSummit()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        return Summit::get()->filter(array(
            'SummitBeginDate:LessThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'SummitEndDate:GreaterThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'Active' => 1
        ))->first();
    }

    /**
     * @return bool
     */
    public function IsCurrent()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        $start = new \DateTime($this->SummitBeginDate, new DateTimeZone('UTC'));
        $end = new \DateTime($this->SummitEndDate, new DateTimeZone('UTC'));
        return $this->Active && $start <= $now && $end >= $now;
    }

    public function IsUpComing()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        $start = new \DateTime($this->SummitBeginDate, new DateTimeZone('UTC'));
        $end = new \DateTime($this->SummitEndDate, new DateTimeZone('UTC'));
        return $this->Active && $start >= $now && $end >= $now;
    }

    public static function GetUpcoming()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        return Summit::get()->filter(array(
            'SummitBeginDate:GreaterThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'SummitEndDate:GreaterThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'Active' => 1
        ))->first();
    }

    private $must_seed = false;

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if($this->ID === 0)
        {
            $this->must_seed = true;
        }
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if($this->must_seed)
        {
            self::seedBasicEventTypes($this->ID);
            self::seedSummitTypes($this->ID);
        }
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return string
     */
    public function getName()
    {
        $value =  $this->getField('Name');
        if(empty($value))
        {
            $value = $this->getField('Title');
        }
        return $value;
    }

    /**
     * @return DateTime
     */
    public function getBeginDate()
    {
        return $this->getSummitBeginDate();
    }

     /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->getSummitEndDate();
    }

    /**
     * @param mixed|null $day
     * @return SummitEvent[]
     * @throws Exception
     */
    public function getSchedule($day = null)
    {
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::equal('Published',1));
        if(!is_null($day))
        {
            if(!$day instanceof DateTime)
                $day = new DateTime($day);

            $start = $day->setTime(0,0,0)->format("Y-m-d H:i:s");
            $end   = $day->add(new DateInterval('PT23H59M59S'))->format("Y-m-d H:i:s");

            $query->addAndCondition(QueryCriteria::greaterOrEqual('StartDate',$this->convertDateFromTimeZone2UTC($start)));
            $query->addAndCondition(QueryCriteria::lowerOrEqual('EndDate', $this->convertDateFromTimeZone2UTC($end)));
        }
        $query->addOrder(QueryOrder::asc('StartDate'));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Events',$query)->toArray();
    }

    /**
     * @param $value
     * @return null|string
     */
    public function convertDateFromTimeZone2UTC($value)
    {
        $time_zone_id   = $this->TimeZone;
        if(empty($time_zone_id)) return $value;
        $time_zone_list = timezone_identifiers_list();

        if(isset($time_zone_list[$time_zone_id]) && !empty($value))
        {
            $utc_timezone      = new DateTimeZone("UTC");
            $time_zone_name = $time_zone_list[$time_zone_id];
            $time_zone   = new \DateTimeZone($time_zone_name);
            $date  = new \DateTime($value, $time_zone);
            $date->setTimezone($utc_timezone);
            return $date->format("Y-m-d H:i:s");
        }
        return null;
    }

    /**
     * @param $value
     * @return null|string
     */
    public function convertDateFromUTC2TimeZone($value)
    {
        $time_zone_id   = $this->TimeZone;
        if(empty($time_zone_id)) return $value;
        $time_zone_list = timezone_identifiers_list();

        if(isset($time_zone_list[$time_zone_id]) && !empty($value))
        {
            $utc_timezone   = new DateTimeZone("UTC");
            $time_zone_name = $time_zone_list[$time_zone_id];
            $time_zone      = new \DateTimeZone($time_zone_name);
            $date           = new \DateTime($value, $utc_timezone);

            $date->setTimezone($time_zone);
            return $date->format("Y-m-d H:i:s");
        }
        return null;
    }

    /**
     * @return ISummitEventType[]
     */
    public function getEventTypes()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'EventTypes');
    }

    /**
     * @param ISummitEventType $type
     * @return void
     */
    public function addEventType(ISummitEventType $event_type)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'EventTypes')->add($event_type);
    }

    /**
     * @return ISummitType[]
     */
    public function getTypes()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Types');
    }

    /**
     * @param ISummitType $type
     * @return void
     */
    public function addType(ISummitType $type)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Types')->add($type);
    }

    /**
     * @return void
     */
    public function clearAllTypes()
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Types')->removeAll();
    }

    /**
     * @return ISummitAirport[]
     */
    public function getAirports()
    {
        $query = new QueryObject(new SummitAirport);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitAirport'));
        $query->addOrder(QueryOrder::asc('Order'));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->toArray();
    }

    /**
     * @param ISummitAirport $airport
     * @return void
     */
    public function addAirport(ISummitAirport $airport)
    {
        $query = new QueryObject(new SummitAirport);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitAirport'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->add($airport);
    }

    /**
     * @return void
     */
    public function clearAllAirports()
    {
        $query = new QueryObject(new SummitAirport);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitAirport'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->removeAll();
    }

    /**
     * @param bool|false $show_all
     * @param string $hotel_type
     * @return array
     * @throws Exception
     */
    public function getHotels($show_all = false, $hotel_type = 'Primary')
    {
        $filters = array('Type' => $hotel_type);

        if(!$show_all)
        {
            $filters['DisplayOnSite'] = true;
        }

        return SummitHotel::get()->filter($filters)->sort('Order', 'ASC')->toArray();
    }

    /**
     * @param ISummitHotel $hotel
     * @return void
     */
    public function addHotel(ISummitHotel $hotel)
    {
        $query = new QueryObject(new SummitHotel);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitHotel'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->add($hotel);
    }

    /**
     * @return void
     */
    public function clearAllHotels()
    {
        $query = new QueryObject(new SummitHotel);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitHotel'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->removeAll();
    }

    /**
     * @return ISummitVenue[]
     */
    public function getVenues()
    {
        $query = new QueryObject(new SummitVenue);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitVenue'));
        $query->addOrder(QueryOrder::asc('Order'));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->toArray();
    }

    /**
     * @param ISummitVenue $venue
     * @return void
     */
    public function addVenue(ISummitVenue $venue)
    {
        $query = new QueryObject(new SummitVenue);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitVenue'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->add($venue);
    }

    /**
     * @return void
     */
    public function clearAllVenues()
    {
        $query = new QueryObject(new SummitVenue);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitVenue'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->removeAll();
    }

    // CMS admin UI


    public function getCMSFields()
    {

        $_REQUEST['SummitID'] = $this->ID;

        $f = new FieldList(
            $rootTab = new TabSet("Root",   $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main',new TextField('Title','Title'));
        $f->addFieldToTab('Root.Main',$link = new TextField('Link','Summit Page Link'));
        $link->setDescription('The link to the site page for this summit. Eg: <em>/summit/vancouver-2015/</em>');
        $f->addFieldToTab('Root.Main',new CheckboxField('Active','This is the active summit'));
        $f->addFieldToTab('Root.Main',$date_label = new TextField('DateLabel','Date label'));
        $date_label->setDescription('A readable piece of text representing the date, e.g. <em>May 12-20, 2015</em> or <em>December 2016</em>');

        $f->addFieldToTab('Root.Main',$registration_link = new TextField('RegistrationLink', 'Registration Link'));
        $registration_link->setDescription('Link to the site where tickets can be purchased.');

        $f->addFieldsToTab('Root.Main', $ddl_timezone = new DropdownField('TimeZone', 'Time Zone', DateTimeZone::listIdentifiers()));
        $ddl_timezone->setEmptyString('-- Select a Timezone --');

        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SummitBeginDate', 'Summit Begin Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SummitEndDate', 'Summit End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SubmissionBeginDate', 'Submission Begin Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SubmissionEndDate', 'Submission End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('VotingBeginDate', 'Voting Begin Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('VotingEndDate', 'Voting End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SelectionBeginDate', 'Selection Begin Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SelectionEndDate', 'Selection End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('RegistrationBeginDate', 'Registration Begin Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('RegistrationEndDate', 'Registration End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $logo_field = new UploadField('Logo','Logo');
        $logo_field->setAllowedMaxFileNumber(1);
        $logo_field->setAllowedFileCategories('image');
        $logo_field->setFolderName('summits/logos/');
        $logo_field->getValidator()->setAllowedMaxFileSize(1048576);
        $f->addFieldToTab('Root.Main', $logo_field);

        $f->addFieldToTab('Root.Main',new TextField('ComingSoonBtnText', 'Coming Soon Btn Text'));
        $f->addFieldToTab('Root.Main',new TextField('ExternalEventId', 'Eventbrite Event Id'));


        if($this->ID > 0) {
            // tracks
            $config     = GridFieldConfig_RecordEditor::create(10);
            $categories = new GridField('Categories', 'Presentation Categories', $this->Categories(), $config);
            $f->addFieldToTab('Root.Presentation Categories', $categories);

            // locations

            $config = GridFieldConfig_RecordEditor::create();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses
            (
                array
                (
                    'SummitVenue' => 'Venue',
                    'SummitHotel' => 'Hotel',
                    'SummitAirport' => 'Airport',
                    'SummitExternalLocation' => 'External Location',
                )
            );
            $config->addComponent($multi_class_selector);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $gridField = new GridField('Locations', 'Locations',
                $this->Locations()->where("ClassName <> 'SummitVenueRoom' "), $config);
            $f->addFieldToTab('Root.Locations', $gridField);

            // types

            $config = GridFieldConfig_RecordEditor::create();
            $gridField = new GridField('Types', 'Types', $this->Types(), $config);
            $f->addFieldToTab('Root.Types', $gridField);

            // event types
            $config = GridFieldConfig_RecordEditor::create();
            $gridField = new GridField('EventTypes', 'EventTypes', $this->EventTypes(), $config);
            $f->addFieldToTab('Root.EventTypes', $gridField);

            //schedule

            $config = GridFieldConfig_RecordEditor::create(50);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->removeComponentsByType('GridFieldDeleteAction');
            $gridField = new GridField('Schedule', 'Schedule', $this->Events()->filter('Published', true)->sort
            (
                array
                (
                    'StartDate' => 'ASC',
                    'EndDate' => 'ASC'
                )
            ), $config);
            $config->getComponentByType("GridFieldDataColumns")->setFieldCasting(array("Description" => "HTMLText->BigSummary"));
            $f->addFieldToTab('Root.Schedule', $gridField);
            $config->addComponent(new GridFieldPublishSummitEventAction);

            // events

            $config = GridFieldConfig_RecordEditor::create(50);
            $config->addComponent(new GridFieldPublishSummitEventAction);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $gridField = new GridField('Events', 'Events', $this->Events()->filter('ClassName', 'SummitEvent'), $config);
            $config->getComponentByType("GridFieldDataColumns")->setFieldCasting(array("Description" => "HTMLText->BigSummary"));
            $f->addFieldToTab('Root.Events', $gridField);

            //track selection list presentations

            $result = DB::query("SELECT DISTINCT SummitEvent.*, Presentation.*
FROM SummitEvent
INNER JOIN Presentation ON Presentation.ID = SummitEvent.ID
INNER JOIN SummitSelectedPresentation ON SummitSelectedPresentation.PresentationID = Presentation.ID
INNER JOIN SummitSelectedPresentationList ON SummitSelectedPresentation.SummitSelectedPresentationListID = SummitSelectedPresentationList.ID
WHERE(ListType = 'Group') AND (SummitEvent.ClassName IN ('Presentation')) AND  (SummitEvent.SummitID = {$this->ID})");

            $presentations = new ArrayList();
            foreach ($result as $row) {
                $presentations->add(new Presentation($row));
            }

            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent(new GridFieldPublishSummitEventAction);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->removeComponentsByType('GridFieldAddNewButton');
            $gridField = new GridField('TrackChairs', 'TrackChairs Selection Lists', $presentations, $config);
            $gridField->setModelClass('Presentation');
            $f->addFieldToTab('Root.TrackChairs Selection Lists', $gridField);


            // attendees

            $config = GridFieldConfig_RecordEditor::create(50);
            $gridField = new GridField('Attendees', 'Attendees', $this->Attendees(), $config);
            $f->addFieldToTab('Root.Attendees', $gridField);

            //tickets types

            $config = GridFieldConfig_RecordEditor::create(50);
            $gridField = new GridField('SummitTicketTypes', 'Ticket Types', $this->SummitTicketTypes(), $config);
            $f->addFieldToTab('Root.TicketTypes', $gridField);

            // promo codes

            $config = GridFieldConfig_RecordEditor::create(50);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();


            $multi_class_selector->setClasses
            (
                array
                (
                    'SpeakerSummitRegistrationPromoCode' => 'Speaker Promo Code',
                )
            );

            $config->addComponent($multi_class_selector);


            $promo_codes = new GridField('SummitRegistrationPromoCodes', 'Registration Promo Codes',
                $this->SummitRegistrationPromoCodes(), $config);
            $f->addFieldToTab('Root.RegistrationPromoCodes', $promo_codes);

            // speakers

            $config = GridFieldConfig_RecordEditor::create(50);
            $gridField = new GridField('Speakers', 'Speakers', $this->Speakers(false), $config);
            $config->getComponentByType("GridFieldDataColumns")->setFieldCasting(array("Bio" => "HTMLText->BigSummary"));
            $f->addFieldToTab('Root.Speakers', $gridField);

            // presentations

            $config = GridFieldConfig_RecordEditor::create(50);
            $config->addComponent(new GridFieldPublishSummitEventAction);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $gridField = new GridField('Presentations', 'Presentations', $this->Presentations()->where(" Title IS NOT NULL AND Title <>'' "), $config);
            $config->getComponentByType("GridFieldDataColumns")->setFieldCasting(array("Description" => "HTMLText->BigSummary"));
            $f->addFieldToTab('Root.Presentations', $gridField);

            // push notifications
            $config = GridFieldConfig_RecordEditor::create(50);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->getComponentByType('GridFieldDataColumns')->setDisplayFields
            (
                array('Channel'=>'Channel','Message'=>'Message', 'Owner.FullName'=>'Owner', 'IsSent' => 'Is Sent?')
            );
            $gridField = new GridField('Notifications', 'Notifications', $this->Notifications(), $config);
            $f->addFieldToTab('Root.Notifications', $gridField);
        }
        return $f;

    }

    protected function validate(){
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;
        $name = trim($this->Title);
        if(empty($name)){
            return $valid->error('Title is required!');
        }

        $count = intval(Summit::get()->filter(array('Title'=>$name, "ID:ExactMatch:not" => $this->ID))->count());

        if($count > 0)
            return $valid->error(sprintf('Summit Title %s already exists!. please set another one', $this->Title));

        $time_zone = $this->TimeZone;
        if(empty($time_zone)){
            return $valid->error('Time Zone is required!');
        }

        $start_date = $this->SummitBeginDate;
        $end_date   = $this->SummitEndDate;
        if(!is_null($start_date) && !is_null($end_date))
        {
            $start_date = new DateTime($start_date);
            $end_date   = new DateTime($end_date);
            if($start_date > $end_date)
                return $valid->error('End Date must be greather than Start Date');
        }

        $start_date = $this->RegistrationBeginDate;
        $end_date   = $this->RegistrationEndDate;

        if(!is_null($start_date) && !is_null($end_date))
        {
            $start_date = new DateTime($start_date);
            $end_date   = new DateTime($end_date);
            if($start_date > $end_date)
                return $valid->error('Registration End Date must be greather than Registration Start Date');
        }
        return $valid;
    }

     /**
     * @param SummitMainInfo $info
     * @return void
     */
    function registerMainInfo(SummitMainInfo $info)
    {
        $this->Name = $info->getName();
        $this->SummitBeginDate = $info->getStartDate();
        $this->SummitEndDate = $info->getEndDate();
    }

    public function isEventInsideSummitDuration(ISummitEvent $summit_event)
    {
        $event_start_date = new DateTime($summit_event->getStartDate());
        $event_end_date   = new DateTime($summit_event->getEndDate());
        $summit_start_date = new DateTime($this->getBeginDate());
        $summit_end_date = new DateTime($this->getEndDate());

        return  $event_start_date >= $summit_start_date && $event_start_date <= $summit_end_date &&
        $event_end_date <= $summit_end_date && $event_end_date >= $event_start_date;
    }

    public function isAttendeesRegistrationOpened()
    {
        $registration_begin_date = $this->RegistrationBeginDate;
        $registration_end_date   = $this->RegistrationEndDate;

        if(is_null($registration_begin_date) || is_null($registration_end_date)) return false;
        $time_zone_list          = timezone_identifiers_list();
        $summit_time_zone        = new DateTimeZone($time_zone_list[$this->TimeZone]);

        $registration_begin_date = new DateTime($registration_begin_date, $summit_time_zone);
        $registration_end_date   = new DateTime($registration_end_date, $summit_time_zone);
        $now                     = new DateTime("now", $summit_time_zone);

        return $now >= $registration_begin_date && $now <=$registration_end_date;
    }

    /**
     * @param string $ticket_external_id
     * @return ISummitTicketType
     */
    public function findTicketTypeByExternalId($ticket_external_id)
    {
       return $this->SummitTicketTypes()->filter('ExternalId', $ticket_external_id)->first();
    }

    /**
     * @param int $summit_id
     * @throws ValidationException
     * @throws null
     */
    public static function seedSummitTypes($summit_id)
    {
        $summit = Summit::get()->byID($summit_id);

        if(!SummitType::get()->filter(array('Title'=>'Main Conference', 'SummitID' => $summit_id))->first()) {
            $main_type              = new SummitType();
            $main_type->Title       = 'Main Conference';
            $main_type->Description = 'This Schedule is for general attendees. Its includes breakout tracks, hand-ons labs, keynotes and sponsored sessions';
            $main_type->Audience    = 'General Attendees';
            $main_type->SummitID    = $summit_id;
            $main_type->StartDate   = $summit->BeginDate;
            $main_type->EndDate     = $summit->EndDate;
            $main_type->Type        = 'MAIN';
            $main_type->write();
        }

        if(!SummitType::get()->filter(array('Title'=>'Design Summit', 'SummitID'=>$summit_id))->first()) {
            $design_type              = new SummitType();
            $design_type->Title       = 'Design Summit';
            $design_type->Description = 'This Schedule is specifically for developers and operators who contribute to the roadmap for the N release cycle. The Design Summit is not a classic track with speakers and presentations and its not the right place to get started or learn the basics of OpenStack. This schedule also Includes the Main Conference Sessions';
            $design_type->Audience    = 'Developers And Operators';
            $design_type->SummitID    = $summit_id;
            $design_type->StartDate   = $summit->BeginDate;
            $design_type->EndDate     = $summit->EndDate;
            $design_type->Type        = 'DESIGN';
            $design_type->write();
        }

    }

    /**
     * @param int $summit_id
     * @throws ValidationException
     * @throws null
     */
    public static function seedBasicEventTypes($summit_id)
    {
        if(!SummitEventType::get()->filter(array('Type'=>'Presentation', 'SummitID'=>$summit_id))->first()) {
            $presentation = new SummitEventType();
            $presentation->Type = 'Presentation';
            $presentation->SummitID = $summit_id;
            $presentation->write();
        }

        if(!SummitEventType::get()->filter(array('Type'=>'Keynotes', 'SummitID'=>$summit_id))->first()) {
            $key_note = new SummitEventType();
            $key_note->Type = 'Keynotes';
            $key_note->SummitID = $summit_id;
            $key_note->write();
        }

        if(!SummitEventType::get()->filter(array('Type'=>'Hand-on Labs', 'SummitID'=>$summit_id))->first()) {
            $hand_on = new SummitEventType();
            $hand_on->Type = 'Hand-on Labs';
            $hand_on->SummitID = $summit_id;
            $hand_on->write();
        }

        if(!SummitEventType::get()->filter(array('Type'=>'Lunch & Breaks', 'SummitID'=>$summit_id))->first()) {
            $key_note = new SummitEventType();
            $key_note->Type = 'Lunch & Breaks';
            $key_note->SummitID = $summit_id;
            $key_note->write();
        }

        if(!SummitEventType::get()->filter(array('Type'=>'Evening Events', 'SummitID'=>$summit_id))->first()) {
            $key_note = new SummitEventType();
            $key_note->Type = 'Evening Events';
            $key_note->SummitID = $summit_id;
            $key_note->write();
        }
    }

    public static function isDefaultEventType($event_type)
    {
        return in_array($event_type, array('Presentation', 'Keynotes', 'Hand-on Labs', 'Lunch & Breaks', 'Evening Events'));
    }

    public function isAttendee() {
        $current_user = Member::currentUser();
        return ($current_user) ? $current_user->isAttendee($this->getIdentifier()) : false;
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    public function getDates()
    {
        $start_date = $this->getBeginDate();
        $end_date   = $this->getEndDate();
        $res        = array();
        foreach($this->getDatesFromRange($start_date, $end_date) as $date)
            array_push($res, new ArrayData(array('Label'=> $date->format('l jS') , 'Date' => $date->format('Y-m-d H:i:s'))));
        return new ArrayList($res);
    }

    public function getDatesWithEvents()
    {
        $list  = array();
        foreach($this->getDates() as $day){
            if($this->hasPublishedEventOn($day->Date))
                array_push($list, $day);
        }
        return new ArrayList($list);
    }

    public function hasPublishedEventOn($day)
    {
        if(!$day instanceof DateTime)
            $day = new DateTime($day);
        $day->setTime(0,0,0);
        $start_date = $day->format('Y-m-d H:i:s');
        $end_date   = $day->add(new DateInterval('PT23H59M59S'))->format('Y-m-d H:i:s');
        $id = $this->ID;
$sql = <<<SQL
SELECT COUNT(E.ID) FROM SummitEvent E
WHERE E.SummitID = {$id} AND StartDate >= '{$start_date}' AND EndDate <= '{$end_date}';
SQL;
        return intval(DB::query($sql)->value()) > 0;
    }

    private function getDatesFromRange($start, $end) {
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(
            new DateTime($start),
            $interval,
            $realEnd
        );

        foreach($period as $date) {
            $array[] = $date->setTime(0,0,0);
        }

        return $array;
    }

    /**
     * @param $event_id
     * @return SummitEvent
     * @throws Exception
     */
    public function getEventFromSchedule($event_id)
    {
        $event = $this->Events()->filter( array('Published' => 1 , 'ID' =>  $event_id))->first();
        if($event->ClassName == 'Presentation')
        {
            $event = Presentation::get()->byID($event_id);
        }
        return $event;
    }

    public function Speakers($only_published  = true)
    {
        $id     = $this->ID;
        $filter = $only_published ? "AND E.Published = 1 " : "";
        $dl     = new DataList('PresentationSpeaker');

        $dl = $dl->leftJoin('Member', ' Member.ID = PresentationSpeaker.MemberID')
            ->where("EXISTS
            (
                SELECT E.ID FROM SummitEvent E
                INNER JOIN Presentation P ON E.ID = P.ID
                INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
                WHERE E.SummitID = {$id}
                {$filter}
                AND PS.PresentationSpeakerID = PresentationSpeaker.ID
            )");
        return $dl;
    }

    public function Tags()
    {
        $id = $this->ID;

        $sql = <<<SQL

SELECT distinct T.* FROM Tag T
INNER JOIN SummitEvent_Tags ET ON ET.TagID = T.ID
INNER JOIN SummitEvent E ON E.ID = ET.SummitEventID
WHERE E.SummitID = {$id}
SQL;

        $list = array();
        $res = DB::query($sql);
        foreach($res as $row)
        {

            $class = $row['ClassName'];
            array_push($list, new $class($row));
        }

        return new ArrayList($list);
    }

    /**
     * @return bool
     */
    public function isCallForSpeakersOpen()
    {
        $start_date = $this->getField('SubmissionBeginDate');
        $end_date   = $this->getField('SubmissionEndDate');

        if(empty($start_date) || empty($end_date)) return false;
        $start_date   = new DateTime($start_date, new DateTimeZone('UTC'));
        $end_date     = new DateTime($end_date, new DateTimeZone('UTC'));
        $now          = new \DateTime('now', new DateTimeZone('UTC'));
        return ( $now >=  $start_date && $now <= $end_date);
    }

    /**
     * @return bool
     */
    public function isVotingOpen()
    {
        $start_date = $this->getField('VotingBeginDate');
        $end_date   = $this->getField('VotingEndDate');

        if(empty($start_date) || empty($end_date)) return false;
        $start_date   = new DateTime($start_date, new DateTimeZone('UTC'));
        $end_date     = new DateTime($end_date, new DateTimeZone('UTC'));
        $now          = new \DateTime('now', new DateTimeZone('UTC'));
        return ( $now >=  $start_date && $now <= $end_date);
    }
}