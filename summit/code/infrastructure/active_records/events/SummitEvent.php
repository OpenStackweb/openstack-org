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
    use SummitEntityMetaTagGenerator;

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
        'AvgFeedbackRate'  => 'Float',
        'RSVPLink'         => 'Text',
        'HeadCount'        => 'Int',
    );

    private static $has_many = array
    (
        'Feedback'        => 'SummitEventFeedback',
        'RSVPSubmissions' => 'RSVP',
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
        'Location'     => 'SummitAbstractLocation',
        'Summit'       => 'Summit',
        'Type'         => 'SummitEventType',
        'RSVPTemplate' => 'RSVPTemplate',
    );

    private static $summary_fields = array
    (
        'Title'                  => 'Event Title',
        'SummitTypesLabel'       => 'Summit Types',
        'StartDate'              => 'Event Start Date',
        'EndDate'                => 'Event End Date',
        'Location.Name'          => 'Location',
        'Type.Type'              => 'Event Type',
    );

    protected function onBeforeDelete()
    {
        parent::onBeforeDelete();
        foreach($this->Feedback() as $e){
            $e->delete();
        }
        foreach($this->RSVPSubmissions() as $e){
            $e->delete();
        }
        $this->AllowedSummitTypes()->removeAll();
        $this->Sponsors()->removeAll();
        $this->Tags()->removeAll();
        $this->Attendees()->removeAll();
    }

    public function getTitle()
    {
        return html_entity_decode($this->getField('Title'));
    }

    public function getRSVPLink()
    {
        return html_entity_decode($this->getField('RSVPLink'));
    }

    public function getFormattedTitle(){
        return sprintf("%s (%s)", $this->getTitle(), $this->Type()->Type);
    }

    public function getTitleAndTime(){
        return sprintf("%s (%s - %s)", $this->getTitle(), $this->getStartDateNice(), $this->getEndDateNice());
    }

    public function SummitTypesLabel()
    {
        $label =  '';
        foreach($this->AllowedSummitTypes() as $st)
            $label .= $st->Title. ' ';
        if(empty($label)) $label = 'NOT SET';
        return $label;
    }

    public function getShortDescription(){
        $val = $this->getField('ShortDescription');
        if(empty($val)){
            $val = $this->getField('Description');
        }
        return $val;
    }

    public function getDescription(){
        $val = $this->getField('Description');
        if(empty($val)){
            $val = $this->getField('ShortDescription');
        }
        return $val;
    }

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

    /**
     * @return bool
     */
    public function isPresentation() {
        return $this instanceof Presentation;
    }

    public function getLink() {
        $page = SummitAppSchedPage::get()->filter('SummitID', $this->SummitID)->first();
        if($page) {
        	return $page->getAbsoluteLiveLink(false).'events/'.$this->getIdentifier().'/'.$this->getTitleForUrl();	
        }
        
    }

    public function getAvgRate() {
        return $this->AvgFeedbackRate;
    }

    public function setAvgRate($avg_rate) {
        $this->AvgFeedbackRate = $avg_rate;
    }

    public function getTitleForUrl() {
        return singleton('SiteTree')->generateURLSegment($this->Title);
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

    public function getLocationCapacity()
    {
        if($this->Location()->ID > 0 && $this->Location()->Capacity)
        {
            return $this->Location()->Capacity;
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

        $date_nice = date('l, F j, g:ia',strtotime($start_date)).'-'.date('g:ia',strtotime($end_date));
        return $date_nice;
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

    public function isAllowedSummitType($summit_type_id) {
        $allowed_summits = $this->getAllowedSummitTypes();
        foreach ($allowed_summits as $summit_type) {
            if ($summit_type->ID == $summit_type_id) return 1;
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
     * @return ISummitEventFeedBack
     */
    public function getCurrentMemberFeedback()
    {
        $member = Member::currentUser();
        return $this->Feedback()->where('OwnerID = '.$member->ID)->filter('ClassName','SummitEventFeedback')->first();
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

        Requirements::customScript("
        jQuery( document ).ready(function() {
            jQuery('body').on('change','#Form_ItemEditForm_RSVPTemplateID',
                function(){
                    jQuery('#Form_ItemEditForm_action_save').click();
                }
            );
        });");

        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new TextField('Title','Title'));
        $f->addFieldToTab('Root.Main', new HtmlEditorField('Description','Description'));
        $f->addFieldToTab('Root.Main', new HtmlEditorField('ShortDescription','Abstract'));
        $f->addFieldToTab('Root.Main', new TextField('HeadCount','HeadCount'));
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

        $f->addFieldsToTab('Root.Main', new ReadonlyField('AvgFeedbackRate', 'AvgFeedbackRate'));
        $f->addFieldsToTab('Root.Main', new TextField('RSVPLink', 'RSVP Link'));
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
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $feedback = new GridField('Feedback', 'Feedback', $this->Feedback(), $config);
            $f->addFieldToTab('Root.Feedback', $feedback);

            // rsvp
            $rsvp_template = new DropdownField('RSVPTemplateID','Select a Template',RSVPTemplate::get()->map());
            $rsvp_template->setEmptyString('-- View All Templates --');
            $f->addFieldToTab('Root.RSVP', $rsvp_template);

            if ($this->RSVPTemplate()->exists()) {
                $config = new GridFieldConfig_RecordEditor(100);
                $config->removeComponentsByType('GridFieldAddNewButton');
                $config->addComponent(new GridFieldAjaxRefresh(1000, false));
                $rsvps = new GridField('RSVPSubmissions', 'RSVP Submissions', $this->RSVPSubmissions(), $config);
                $f->addFieldToTab('Root.RSVP', $rsvps);
            } else {
                $f->addFieldToTab('Root.RSVP', LiteralField::create('AddNew','Or add a new one'));
                $config = new GridFieldConfig_RecordEditor(100);
                $rsvp_templates = new GridField('RSVPTemplates', 'RSVP Templates', RSVPTemplate::get(), $config);
                $f->addFieldToTab('Root.RSVP', $rsvp_templates);
            }


        }
        return $f;
    }

    public function publish()
    {
        if($this->Published)
            throw new EntityValidationException('Already published Summit Event');

        $validation_result = $this->validate();

        if(!$validation_result->valid())
        {
            throw new EntityValidationException($validation_result->messageList());
        }

        if(intval($this->AllowedSummitTypes()->count()) === 0)
            throw new EntityValidationException('To publish this event you must associate a valid summit type!');

        $start_date = $this->getStartDate();
        $end_date   = $this->getEndDate();

        if((empty($start_date) || empty($end_date)))
            throw new EntityValidationException('To publish this event you must define a start/end datetime!');

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
        return  (bool)$this->Published;
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

                $start_date      = $summit->convertDateFromTimeZone2UTC( $this->getStartDate());
                $end_date        = $summit->convertDateFromTimeZone2UTC( $this->getEndDate() );
                $id              = $this->getIdentifier();
                $location_id     = $this->LocationID;

                if(empty($start_date) || empty($end_date))
                    return $valid;

                if(!$this->isPublished()) return $valid;

                $query = <<<SQL
SELECT COUNT(E.ID) FROM SummitEvent E
WHERE
E.SummitID  = {$summit_id}  AND
E.Published = 1             AND
E.StartDate < '{$end_date}' AND
'{$start_date}' < E.EndDate AND
E.ID <> $id                 AND
E.LocationID = $location_id AND
E.LocationID <> 0;
SQL;
                $qty = intval(DB::query($query)->value());

                if($qty > 0)
                {
                    return $valid->error('There is another event assigned for current location  on that date/time range !');
                }
            }
        }
        return $valid;
    }

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
        return $date->format('g:ia');
    }

    public function getStartTimeHMS()
    {
        $date = new Datetime($this->getStartDate());
        return $date->format('h:i:s A');
    }

    public function getBeginDateYMD()
    {
        $date = new DateTime($this->getStartDate());

        return $date->format('Y-m-d');
    }

    public function getDayLabel()
    {
        $date = new DateTime($this->getStartDate());

        return $date->format('l j');
    }

    public function getEndDateYMD()
    {
        $date = new DateTime($this->getEndDate());

        return $date->format('Y-m-d');
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
        return $date->format('g:ia');
    }

    public function getEndTimeHMS()
    {
        $date = new Datetime($this->getEndDate());
        return $date->format('h:i:s A');
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

    public function canDelete($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    public function getTags()
    {
        return $this->getManyManyComponents('Tags');
    }

    /**
     * @return int
     */
    public function AttendeesScheduleCount()
    {
        $res = DB::query("SELECT COUNT(ID) AS QTY FROM SummitAttendee_Schedule WHERE SummitEventID = {$this->ID};")->first();
        return intval($res['QTY']);
    }
}