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
        'Title'                     => 'Text',
        'Abstract'                  => 'HTMLText',
        'SocialSummary'             => 'VarChar(100)',
        'StartDate'                 => 'SS_Datetime',
        'EndDate'                   => 'SS_Datetime',
        'Published'                 => 'Boolean',
        'PublishedDate'             => 'SS_Datetime',
        'AllowFeedBack'             => 'Boolean',
        'AvgFeedbackRate'           => 'Float',
        'HeadCount'                 => 'Int',
        'RSVPLink'                  => 'Text',
        'RSVPMaxUserNumber'         => 'Int',
        'RSVPMaxUserWaitListNumber' => 'Int',
        'Occupancy'                 => 'Enum(array("EMPTY","25%","50%", "75%","FULL"), "EMPTY")',
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
        'Sponsors'           => 'Company',
        'Tags'               => 'Tag',
    );

    private static $belongs_many_many = array
    (
        'Attendees'   => 'Member',
    );

    private static $has_one = array
    (
        'Location'       => 'SummitAbstractLocation',
        'Summit'         => 'Summit',
        'Type'           => 'SummitEventType',
        'RSVPTemplate'   => 'RSVPTemplate',
        'Category'       => 'PresentationCategory',
    );

    private static $summary_fields = array
    (
        'Title'                  => 'Event Title',
        'StartDate'              => 'Event Start Date',
        'EndDate'                => 'Event End Date',
        'Location.Name'          => 'Location',
        'Type.Type'              => 'Event Type',
        'Occupancy'              => 'Room Occupancy',
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
        $this->Sponsors()->removeAll();
        $this->Tags()->removeAll();
        $this->Attendees()->removeAll();
    }

    public function getTitle()
    {
        return html_entity_decode($this->getField('Title'));
    }

    public function getAbstractJson(){
        return json_encode($this->Abstract);
    }

    public function getTitleJson(){
        return json_encode($this->Title);
    }

    public function getSocialSummaryJson(){
        return json_encode($this->SocialSummary);
    }

    public function getSummitTitle()
    {
        return $this->Summit->Title;
    }

    public function getRSVPLink()
    {
        return html_entity_decode($this->getField('RSVPLink'));
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public function getRSVPURL($absolute = true)
    {
        return $this->hasRSVPTemplate() ?
            $this->getLink('show', $absolute).'/rsvp': $this->getRSVPLink();
    }

    /**
     * @return bool
     */
    public function isExternalRSVP(){
        return !$this->hasRSVPTemplate() ;
    }

    /**
     * @return bool
     */
    public function hasRSVP(){
        return !empty($this->getRSVPURL());
    }

    public function getFormattedTitle(){
        return sprintf("%s (%s)", $this->getTitle(), $this->Type()->Type);
    }

    public function getTitleAndTime(){
        return sprintf("%s (%s)", $this->getTitle(), $this->getDateNice());
    }

    private static $searchable_fields = array
    (
        'Title',
        'StartDate',
        'Description',
        'Abstract',
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

    /**
     * @param string $type
     * @param bool $absolute
     * @return null|string
     */
    public function getLink($type ='show', $absolute = true) {
        if($type == 'show') {
            $page = SummitAppSchedPage::getBy($this->Summit());
            if ($page) {
                if($absolute)
                    return Controller::join_links(Director::absoluteBaseURL(), $page->RelativeLink() , 'events' , $this->getIdentifier() ,$this->getTitleForUrl());
                return Controller::join_links($page->RelativeLink(), 'events' , $this->getIdentifier() , $this->getTitleForUrl());
            }
        }
        return null;
    }

    public function Link() {
        return $this->getLink();
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
     * @param string $type
     * @return bool
     */
    public function isOfType($type)
    {
        return $this->getTypeName() == $type;
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
        return $this->Feedback()->filter('ClassName','SummitEventFeedback')->sort("Created", "DESC");
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
        $f->addFieldToTab('Root.Main', new HtmlEditorField('Abstract','Abstract'));
        $f->addFieldToTab('Root.Main', new TextField('SocialSummary','Social Summary (100 Chars)'));
        $f->addFieldToTab('Root.Main', new TextField('HeadCount','HeadCount'));
        $f->addFieldToTab('Root.Main', $ddl_track = new DropdownField('CategoryID','Category', PresentationCategory::get()->filter('SummitID', $summit_id)->map('ID', 'Title')));
        $f->addFieldToTab('Root.Main', new DropdownField('Occupancy','Room Occupancy', $this->dbObject('Occupancy')->enumValues()));

        $ddl_track->setEmptyString("-- Select a Category --");

        $f->tag('Tags', 'Tags', Tag::get(), $this->Tags())
        ->configure()
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
            $ddl_type = new DropdownField
            (
                'TypeID',
                'Event Type',
                SummitEventType::get()->filter('SummitID', $summit_id)->map('ID', 'Type')
            )
        );

        $ddl_type->setEmptyString('-- Select a Event Type --');

        $f->addFieldToTab('Root.Main', new HiddenField('SummitID','SummitID'));

        if($this->ID > 0)
        {

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

            $f->addFieldsToTab('Root.RSVP', new TextField('RSVPLink', 'RSVP External Link'));

            $rsvp_template = new DropdownField('RSVPTemplateID','Select a Template', RSVPTemplate::get()->filter('SummitID', $summit_id)->map());
            $rsvp_template->setEmptyString('-- View All Templates --');
            $f->addFieldToTab('Root.RSVP', LiteralField::create('AddNew','Or add a new custom RSVP Configuration'));
            $f->addFieldToTab('Root.RSVP', $rsvp_template);
            $f->addFieldToTab('Root.RSVP', new NumericField('RSVPMaxUserNumber', 'Max # Number'));
            $f->addFieldToTab('Root.RSVP', new NumericField('RSVPMaxUserWaitListNumber', 'Max # Wait List'));
            $f->addFieldToTab('Root.RSVP', $rsvp_template);

            if ($this->RSVPTemplate()->exists()) {

                $config = new GridFieldConfig_RecordEditor(100);
                $config->removeComponentsByType('GridFieldAddNewButton');
                $config->addComponent(new GridFieldAjaxRefresh(1000, false));
                $rsvps = new GridField('RSVPSubmissions', 'RSVP Submissions', $this->RSVPSubmissions(), $config);
                $f->addFieldToTab('Root.RSVP', $rsvps);
            }

        }

        if($this->ID > 0){
            $_REQUEST['SummitEventID'] = $this->ID;
        }

        return $f;
    }

    /**
     * @return string
     */
    public function getCurrentRSVPSubmissionSeatType(){
        if(!$this->hasRSVPTemplate()) return 'N/A';
        $count_regular = $this->RSVPSubmissions()->filter('SeatType', IRSVP::SeatTypeRegular )->count();
        if($count_regular < intval($this->RSVPMaxUserNumber)) return IRSVP::SeatTypeRegular;
        $count_wait = $this->RSVPSubmissions()->filter('SeatType', IRSVP::SeatTypeWaitList )->count();
        if($count_wait < intval($this->RSVPMaxUserWaitListNumber)) return IRSVP::SeatTypeWaitList;
        return 'FULL';
    }

    /**
     * @param string $seat_type
     * @return bool
     */
    public function couldAddSeatType($seat_type){
        switch($seat_type){
            case IRSVP::SeatTypeRegular: {
                $count_regular = $this->RSVPSubmissions()->filter('SeatType', IRSVP::SeatTypeRegular)->count();
                return $count_regular < intval($this->RSVPMaxUserNumber);
            }
            case IRSVP::SeatTypeWaitList: {
                $count_wait = $this->RSVPSubmissions()->filter('SeatType', IRSVP::SeatTypeWaitList)->count();
                return $count_wait < intval($this->RSVPMaxUserWaitListNumber);
            }
        }
        return false;
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

        $start_date = $this->getStartDate();
        $end_date   = $this->getEndDate();

        if((empty($start_date) || empty($end_date)))
            throw new EntityValidationException('To publish this event you must define a start/end datetime!');

        $this->Published     = true;
        $this->PublishedDate = CustomMySQLDatabase::nowRfc2822();
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

    protected $exclude_type_validation = false;
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

        if(empty($this->Title)){
            return $valid->error('Title  is mandatory!');
        }

        $start_date = $this->getStartDate();
        $end_date   = $this->getEndDate();

        if((empty($start_date) || empty($end_date)) && $this->isPublished())
            return $valid->error('To publish this event you must define a start/end datetime!');

        $type_id = intval($this->TypeID);
        if(!$this->exclude_type_validation && $type_id === 0)
        {
            return $valid->error('You must select an event type!');
        }

        if(!empty($start_date) && !empty($end_date))
        {
            $timezone = $summit->TimeZoneIdentifier;

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

    /**
     * @return string
     */
    public function getStartDateUTC(){
        return  $this->getField('StartDate');
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

    /**
     * @return string
     */
    public function getEndDateUTC()
    {
        return $this->getField('EndDate');
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

        return $current_user->isOnMySchedule($this->getIdentifier());
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
        $res = DB::query("SELECT COUNT(ID) AS QTY FROM Member_Schedule WHERE SummitEventID = {$this->ID};")->first();
        return intval($res['QTY']);
    }

    /**
     * @return bool
     */
    public function hasRSVPTemplate(){
        return intval($this->RSVPTemplateID) > 0 ;
    }

    /**
     * @return bool
     */
    public function allowSpeakers()
    {
        return false;
    }

    /**
     * @param string $seat_type
     * @return int
     */
    public function getCurrentSeatsCountByType($seat_type)
    {
        return $this->RSVPSubmissions()->filter('SeatType', $seat_type)->count();
    }

    /**
     * @return string
     */
    public function getAbstract()
    {
       return $this->getField('Abstract');
    }

    public function AllowFeedBack(){
        return $this->AllowFeedBack;
    }

    public function hasEnded(){
        return $this->getSummit()->getLocalTime() > $this->getEndDate();
    }
}