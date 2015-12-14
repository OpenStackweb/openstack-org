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
class SummitEvent extends DataObject implements ISummitEvent
{
    private static $db = array
    (
        'Title'            => 'Text',
        'Description'      => 'HTMLText',
        'ShortDescription' => 'HTMLText',
        'StartDate'        => 'SS_Datetime',
        'EndDate'          => 'SS_Datetime',
        'Published'        => 'Boolean',
        'PublishedDate'    => 'SS_Datetime',
        'AllowFeedBack'    => 'Boolean',
    );

    private static $has_many = array
    (
        'Feedback' => 'SummitEventFeedback',
    );

    private static $defaults = array
    (
    );

    private static $many_many = array
    (
        'AllowedSummitTypes' => 'SummitType',
        'Sponsors'           => 'Company',
        'Tags'               => 'Tag',
    );

    private static $belongs_many_many = array
    (
        'Attendees'   => 'SummitAttendee',
    );

    private static $has_one = array
    (
        'Location' => 'SummitAbstractLocation',
        'Summit'   => 'Summit',
        'Type'     => 'SummitEventType',
    );

    private static $summary_fields = array
    (
        'Title'                  => 'Event Title',
        'Description'            => 'Description',
        'StartDate'              => 'Event Start Date',
        'EndDate'                => 'Event End Date',
        'Location.Name'          => 'Location',
        'Type.Type'              => 'Event Type',
    );

    private static $searchable_fields = array
    (
        'Title',
        'StartDate',
        'Description',
        'ShortDescription',
        'EndDate',
        'Location.Name',
        'Type.Type',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function isPresentation() {
        return ($this->getTypeName() == 'Presentation');
    }

    public function getLink() {
        return $this->Summit()->Link.'schedule/events/'.$this->getIdentifier().'/'.$this->getTitleForUrl();
    }

    public function getTitleForUrl() {
        $lcase_title = strtolower(trim($this->Title));
        $title_for_url = str_replace(' ','-',$lcase_title);
        return $title_for_url;
    }

    public function getLocationName()
    {
        if($this->Location()->ID > 0)
        {
            return $this->Location()->Name;
        }
        return 'TBD';
    }

    public function getLocationNameNice()
    {
        if($this->Location()->ID > 0)
        {
            return $this->Location()->getFullName();
        }
        return 'TBD';
    }

    public function getStartDateNice()
    {
        $start_date =  $this->getStartDate();
        if(empty($start_date)) return 'TBD';
        return $start_date;
    }

    public function getEndDateNice()
    {
        $end_date  = $this->getEndDate();
        if(empty($end_date)) return 'TBD';
        return $end_date;
    }

    public function getDateNice() {
        $start_date = $this->getStartDateNice();
        $end_date = $this->getEndDateNice();
        $date_nice = '';

        if ($start_date == 'TBD' || $end_date == 'TBD') return $start_date;

        $date_nice = date('l j-F, g:i a',strtotime($start_date)).' / '.date('g:i a',strtotime($end_date));
        return $date_nice;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getField('Description');
    }

    public function getShortDescription($length=200){
        $description = strip_tags($this->getField('Description'));

        if (strlen($description) < $length) return $description;

        $pos=strpos($description, ' ', $length);
        $short_desc = substr($description,0,$pos ).'...';
        return $short_desc;
    }

    /**
     * @return ISummitLocation
     */
    public function getLocation()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Location')->getTarget();
    }

    /**
     * @return ICompany[]
     */
    public function getSponsors()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Sponsors')->toArray();
    }

    /**
     * @return ISummitEventType
     */
    public function getType()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Type')->getTarget();
    }

    public function getTypeName()
    {
        return $this->getType()->Type;
    }

    /**
     * @return ISummitType[]
     */
    public function getAllowedSummitTypes()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'AllowedSummitTypes')->toArray();
    }

    public function isAllowedSummitType($summit_type_name) {
        $allowed_summits = $this->getAllowedSummitTypes();
        foreach ($allowed_summits as $summit_type) {
            if ($summit_type->Title == $summit_type_name) return 1;
        }

        return 0;
    }

    /**
     * @return ISummit
     */
    public function getSummit()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Summit')->getTarget();
    }

    /**
     * @return ISummitEventFeedBack[]
     */
    public function getFeedback()
    {
        return $this->Feedback()->filter('ClassName','SummitEventFeedback');
    }

    /**
     * @param ISummitLocation $location
     * @return void
     */
    public function registerLocation(ISummitLocation $location)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Location')->setTarget($location);
    }

    /**
     * @param ICompany $company
     * @return void
     */
    public function addSponsor(ICompany $company)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Sponsors')->add($company);
    }

    /**
     * @return void
     */
    public function clearAllSponsors()
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Sponsors')->removeAll();
    }

    /**
     * @param ISummitEventType $type
     * @return void
     */
    public function setType(ISummitEventType $type)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Type')->setTarget($type);
    }

    /**
     * @param ISummitType $summit_type
     * @return void
     */
    public function addAllowedSummitType(ISummitType $summit_type)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'AllowedSummitTypes')->add($summit_type);
    }

    /**
     * @return void
     */
    public function clearAllAllowedSummitTypes()
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'AllowedSummitTypes')->removeAll();
    }

    /**
     * @param ISummitEventFeedBack $feedback
     * @return void
     */
    public function addFeedback(ISummitEventFeedBack $feedback)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Feedback')->add($feedback);
    }

    /**
     * @return void
     */
    public function clearAllFeedback()
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Feedback')->removeAll();
    }

    public function getCMSFields()
    {

        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new TextField('Title','Title'));
        $f->addFieldToTab('Root.Main', new HtmlEditorField('Description','Description'));
        $f->addFieldToTab('Root.Main', new HtmlEditorField('ShortDescription','Short Description'));
        $f->tag('Tags', 'Tags', Tag::get(), $this->Tags())->configure()
        ->setTitleField('Tag')
        ->end();
        $f->addFieldToTab('Root.Main', new CheckboxField('AllowFeedBack','Is feedback allowed?'));
        $f->addFieldToTab('Root.Main', new HiddenField('SummitID','SummitID'));
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('StartDate', 'Start Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldToTab('Root.Main',$date = new DatetimeField('EndDate', 'End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');


        $locations = SummitAbstractLocation::get()
            ->filter('SummitID', $summit_id )
            ->filter('ClassName', array('SummitVenue', 'SummitVenueRoom', 'SummitExternalLocation') );

        $locations_source = array();

        foreach($locations as $l)
        {
            $locations_source[$l->ID] = $l->getFullName();
        }

        $f->addFieldToTab
        (
            'Root.Main',
            $ddl_location = new DropdownField
            (
                'LocationID',
                'Location',
                $locations_source
            )
        );

        $ddl_location->setEmptyString('-- Select a Location --');

        $f->addFieldToTab
        (
            'Root.Main',
            $ddl_location = new DropdownField
            (
                'TypeID',
                'Event Type',
                SummitEventType::get()->filter('SummitID', $summit_id)->map('ID', 'Type')
            )
        );

        $ddl_location->setEmptyString('-- Select a Event Type --');

        $f->addFieldToTab('Root.Main', new HiddenField('SummitID','SummitID'));

        if($this->ID > 0)
        {

            // summits types
            $config = new GridFieldConfig_RelationEditor(100);
            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
            $completer->setSearchList(SummitType::get()->filter('SummitID', $summit_id));
            $summit_types = new GridField('AllowedSummitTypes', 'Summit Types', $this->AllowedSummitTypes(), $config);
            $f->addFieldToTab('Root.Main', $summit_types);

            // sponsors
            $config = new GridFieldConfig_RelationEditor(100);
            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $sponsors = new GridField('Sponsors', 'Sponsors', $this->Sponsors(), $config);
            $f->addFieldToTab('Root.Sponsors', $sponsors);

            // feedback
            $config = new GridFieldConfig_RecordEditor(100);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $feedback = new GridField('Feedback', 'Feedback', $this->Feedback(), $config);
            $f->addFieldToTab('Root.Feedback', $feedback);
        }
        return $f;
    }

    public function publish()
    {
        if($this->Published)
            throw new EntityValidationException(array('Already published Summit Event'));

        $validation_result = $this->validate();

        if(!$validation_result->valid())
        {
            throw new EntityValidationException($validation_result->messageList());
        }

        if(intval($this->AllowedSummitTypes()->count()) === 0)
            throw new EntityValidationException(array('To publish this event you must associate a valid summit type!'));

        $start_date = $this->getStartDate();
        $end_date   = $this->getEndDate();

        if((empty($start_date) || empty($end_date)))
            throw new EntityValidationException(array('To publish this event you must define a start/end datetime!'));

        $this->Published     = true;
        $this->PublishedDate = MySQLDatabase56::nowRfc2822();
    }

    public function setStartDate($value)
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if(!empty($value))
        {
            $value = $summit->convertDateFromTimeZone2UTC($value);
            $this->setField('StartDate', $value);
        }
    }

    public function setEndDate($value)
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if(!empty($value))
        {
            $value = $summit->convertDateFromTimeZone2UTC($value);
            $this->setField('EndDate', $value);
        }
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return  $this->Published;
    }

    /**
     * @return void
     */
    public function unPublish()
    {
        $this->Published = false;
        $this->PublishedDate = null;
    }

    /**
     * @return ValidationResult
     */
    protected function validate()
    {
        $valid = parent::validate();

        if(!$valid->valid()) return $valid;

        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $summit   = Summit::get()->byID($summit_id);

        if(!$summit){
            return $valid->error('Invalid Summit!');
        }

        $start_date = $this->getStartDate();
        $end_date   = $this->getEndDate();

        if((empty($start_date) || empty($end_date)) && $this->isPublished())
            return $valid->error('To publish this event you must define a start/end datetime!');

        $type_id = intval($this->TypeID);
        if($type_id === 0)
        {
            return $valid->error('You must select an event type!');
        }

        if(!empty($start_date) && !empty($end_date))
        {
            $timezone = $summit->TimeZone;

            if(empty($timezone)){
                return $valid->error('Invalid Summit TimeZone!');
            }

            $start_date = new DateTime($start_date);
            $end_date   = new DateTime($end_date);

            if($end_date < $start_date)
                return $valid->error('start datetime must be greather or equal than end datetime!');

            if(!$summit->isEventInsideSummitDuration($this))
                return $valid->error(sprintf('start/end datetime must be between summit start/end datetime! (%s - %s)', $summit->getBeginDate(), $summit->getEndDate()));

            // validate start time/end time and location
            if(!empty($this->LocationID))
            {
                // validate that each speakers is assigned one time at one location
                $start_date      = $summit->convertDateFromTimeZone2UTC( $this->getStartDate());
                $end_date        = $summit->convertDateFromTimeZone2UTC( $this->getEndDate() );

                $presentation_id = $this->getIdentifier();
                $location_id     = $this->LocationID;

                if(empty($start_date) || empty($end_date))
                    return $valid;

                $query = <<<SQL
SELECT COUNT(P.ID) FROM Presentation P
INNER JOIN SummitEvent E ON E.ID = P.ID
WHERE
E.Published = 1              AND
E.StartDate <= '{$end_date}'  AND
'{$start_date}' <= E.EndDate AND
E.ID <> $presentation_id     AND
E.LocationID = $location_id  AND
E.LocationID <> 0;
SQL;

                $qty = intval(DB::query($query)->value());

                if($qty > 0)
                {
                    return $valid->error('There is another event asigned for current location  on that date/time range !');
                }
            }
        }
        return $valid;
    }

    public function getSpeakers() {
        return new ArrayList();
    }

    /*public function getAtendees() {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this , 'Attendees');
    }*/

    /**
     * @return string
     */
    public function getStartDate()
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        $value = $this->getField('StartDate');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

    public function getStartTime()
    {
        $date = new Datetime($this->getStartDate());
        return $date->format('g:i a');
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        $value = $this->getField('EndDate');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

    public function getEndTime()
    {
        $date = new Datetime($this->getEndDate());
        return $date->format('g:i a');
    }

    public function isScheduled() {
        $current_user = Member::currentUser();
        if (!$current_user) return false;

        $attendee = $current_user->getSummitAttendee($this->Summit->getIdentifier());

        return $attendee->isScheduled($this->getIdentifier());
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
}