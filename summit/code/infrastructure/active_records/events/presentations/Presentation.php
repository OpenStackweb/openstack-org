<?php

/**
 * Class Presentation
 */
class Presentation extends SummitEvent implements IPresentation
{

    /**
     * Defines the phase that a presentation has been created, but
     * no information has been saved to it.
     */
    const PHASE_NEW = 0;


    /**
     * Defines the phase where a presenation has been given a summary,
     * but no speakers have been added
     */
    const PHASE_SUMMARY = 1;


    /**
     * Deinfes a phase where a presentation has a summary and speakers
     */
    const PHASE_SPEAKERS = 2;


    /**
     * Defines a phase where a presentation has been submitted successfully
     */
    const PHASE_COMPLETE = 3;


    const STATUS_RECEIVED = 'Received';


    private static $db = array
    (
        'Level'                   => "Enum('Beginner,Intermediate,Advanced')",
        'Status'                  => 'Varchar',
        'OtherTopic'              => 'Varchar',
        'Progress'                => 'Int',
        'Views'                   => 'Int',
        'BeenEmailed'             => 'Boolean',
        'ProblemAddressed'        => 'HTMLText',
        'AttendeesExpectedLearnt' => 'HTMLText',
        'SelectionMotive'         => 'HTMLText',
    );

    private static $defaults = array
    (
        'TrackChairGivenOrder' => 0,
        'AllowFeedBack' => 1
    );

    private static $has_many = array
    (
        'Votes'            => 'PresentationVote',
        // this is related to track chairs app
        'Comments'         => 'SummitPresentationComment',
        'ChangeRequests'   => 'SummitCategoryChange',
        'Materials'        => 'PresentationMaterial',
    );

    private static $many_many = array
    (
        'Speakers' => 'PresentationSpeaker',
        'Topics'   => 'PresentationTopic',
    );

    static $many_many_extraFields = array(
        'Speakers' => array
        (
            'IsCheckedIn' => "Boolean",
        ),
    );

    private static $has_one = array
    (
        'Creator'   => 'Member',
        'Category'  => 'PresentationCategory',
        'Moderator' => 'PresentationSpeaker',
    );

    private static $summary_fields = array
    (
        'Created'          => 'Created',
        'Title'            => 'Event Title',
        'SummitTypesLabel' => 'Summit Types',
        'Level'            => 'Level',
        'SelectionStatus'  => 'Status',
    );

    public function onBeforeWrite() {
       parent::onBeforeWrite();
       $this->assignEventType();
    }

    private function assignEventType()
    {
        $summit_id = intval($this->SummitID);
        if($summit_id > 0 && intval($this->TypeID) === 0) {
            Summit::seedBasicEventTypes($summit_id);
            $event_type   = SummitEventType::get()->filter(array('Type'=>'Presentation', 'SummitID'=>$summit_id))->first();
            $this->TypeID = $event_type->ID;
        }
    }

    public function getTypeName()
    {
        return 'Presentation';
    }

    /**
     * Gets a link to the presentation
     *
     * @return  string
     */
    public function Link()
    {
        return PresentationPage::get()->filter('SummitID', $this->SummitID)->first()->Link('show/'.$this->ID);
    }

    /**
     * Gets a link to edit this presentation
     *
     * @return  string
     */
    public function EditLink()
    {
        if($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(),'manage', $this->ID, 'summary');
        }
    }

    /**
     * Gets a link to edit this presentation
     *
     * @return  string
     */
    public function EditTagsLink()
    {
        if($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(),'manage', $this->ID, 'tags');
        }
    }


    /**
     * Gets a link to the preview iframe
     *
     * @return  string
     */
    public function PreviewLink() {
        if($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(),'manage', $this->ID, 'preview');
        }
    }


    /**
     * Gets a link to edit the speakers of the presentation
     *
     * @return  string
     */
    public function EditSpeakersLink() {
        if($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(),'manage', $this->ID, 'speakers');
        }
    }

    /**
     * Gets a link to delete this presentation
     *
     * @return  string
     */
    public function DeleteLink() {
        if($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(),'manage', $this->ID, 'delete','?t='.SecurityToken::inst()->getValue());
        }
    }


    public function PreviewHTML()
    {
        $template = new SSViewer('PresentationPreview');

        return $template->process(ArrayData::create(array(
            'Presentation' => $this
        )));
    }


    /**
     * Determines if a track chair can assign this presentation to a seleciton list
     *
     * @return boolean
     */

    public function canAssign() {

        // see if they have either of the appropiate permissions
        if(!Permission::check('TRACK_CHAIR')) return false;

        // see if they are a chair of this particular track
        $IsTrackChair = $this->Category()->TrackChairs('MemberID = '.Member::currentUser()->ID);
        if ($IsTrackChair->Count() != 0) return TRUE;

    }

    /**
     * Determines if the user can create a presentation
     *
     * @return  boolean
     */
    public function canCreate($member = null) {
        return Member::currentUser();
    }


    /**
     * Determines if the user can delete a presentation
     *
     * @return  boolean
     */
    public function canDelete($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE") || $this->CreatorID == Member::currentUserID();
    }


    /**
     * A custom permission for removing (not deleting) speakers
     * @param  Member $member
     * @return boolean         [description]
     */
    public function canRemoveSpeakers($member = null) {
        return true;
    }


    /**
     * Sets a vote for this presentation by the current user
     *
     * @param  $vote int
     */
    public function setUserVote($vote) {
        $v = $this->Votes()->filter('MemberID', Member::currentUserID())->first() ?: PresentationVote::create();
        $v->MemberID = Member::currentUserID();
        $v->PresentationID = $this->ID;
        $v->Vote = $vote;
        $v->write();
    }


    /**
     * Gets the vote on this presentation by the current user
     * @return int
     */
    public function getUserVote()
    {
        return $this->Votes()->filter(array(
            'MemberID' => Member::currentUserID()
        ))->first();
    }


    public function CalcTotalPoints() {
        $sqlQuery = new SQLQuery(
            "SUM(Vote)",
            "PresentationVote",
            "PresentationID = ".$this->ID
        );
        return $sqlQuery->execute()->value();
    }

    public function CalcVoteCount() {
        $sqlQuery = new SQLQuery(
            "COUNT(ID)",
            "PresentationVote",
            "PresentationID = ".$this->ID
        );
        return $sqlQuery->execute()->value();
    }

    public function CalcVoteAverage() {
        $sqlQuery = new SQLQuery(
            "AVG(Vote)",
            "PresentationVote",
            "PresentationID = ".$this->ID
        );
        return round($sqlQuery->execute()->value(), 2);
    }

    /**
     * Determines if the presentation is "new." Since presentations are
     * optimistically written to the database, a simple isInDB() check
     * is not sufficient
     *
     * @return boolean
     */
    public function isNew()
    {
        return $this->Progress == self::PHASE_NEW;
    }

    public function creatorIsSpeaker()
    {
        $c = $this->Speakers()->filter(array(
            'MemberID' => $this->CreatorID
        ));
        if ($c->count()) return true;
    }

    public function creatorBeenEmailed()
    {
        return $this->BeenEmailed;
    }

    public function clearBeenEmailed() {
        $this->BeenEmailed = false;
        $this->write();
    }

    /**
     * Used by the track chair app to allow comments on presentations.
     * Comments are only displayed in the track chair interface.
     **/

    public function addComment($commentBody, $MemberID, $is_category_change_suggestion = false) {
        $comment = new SummitPresentationComment();
        $comment->Body = $commentBody;
        $comment->CommenterID = $MemberID;
        $comment->PresentationID = $this->ID;
        $comment->IsCategoryChangeSuggestion = $is_category_change_suggestion;
        $comment->write();
    }

    /**
     * Used by the track chair app to allow chairs to add a presentation to a personal list.
     **/

    public function assignToIndividualList() {


        // Check permissions of user on talk
        if ($this->CanAssign()) {

            $MySelections = SummitSelectedPresentationList::getMemberList($this->CategoryID);


            // See if the presentation has already been assigned
            $AlreadyAssigned = $MySelections->SummitSelectedPresentations('PresentationID = ' . $this->ID);


            if ($AlreadyAssigned->count() == 0) {

                // Find the higest order value assigned up to this point
                $HighestOrderInList =  $MySelections
                    ->SummitSelectedPresentations()
                    ->sort('Order DESC')
                    ->first()
                    ->Order;

                $SelectedPresentation = new SummitSelectedPresentation();
                $SelectedPresentation->SummitSelectedPresentationListID = $MySelections->ID;
                $SelectedPresentation->PresentationID = $this->ID;
                $SelectedPresentation->MemberID = Member::currentUser()->ID;
                // Place at bottom of list
                $SelectedPresentation->Order = $HighestOrderInList + 1;
                $SelectedPresentation->write();
            }
        }
    }

    /**
     * Used by the track chair app to allow chairs to remove a presentation from a personal list.
     **/

    public function removeFromIndividualList() {


        // Check permissions of user on talk
        if ($this->CanAssign()) {

            $MySelections = SummitSelectedPresentationList::getMemberList($this->CategoryID);

            // See if the presentation has already been assigned
            $AlreadyAssigned = $MySelections->SummitSelectedPresentations('PresentationID = ' . $this->ID)->first();

            if (!is_null($AlreadyAssigned)) {
                $AlreadyAssigned->delete();
            }
        }
    }


    /**
     * Used by the track chair app to allow chairs to add a presentation to a group list.
     **/

    public function assignToGroupList() {


        // Check permissions of user on talk
        if ($this->CanAssign()) {

            $GroupList = SummitSelectedPresentationList::get()
                ->filter(array(
                    'CategoryID' => $this->CategoryID,
                    'ListType' => 'Group'
                ))
                ->first();

            // See if the presentation has already been assigned
            $AlreadyAssigned = $GroupList->SummitSelectedPresentations('PresentationID = ' . $this->ID);


            if ($AlreadyAssigned->count() == 0) {

                // Find the higest order value assigned up to this point
                $HighestOrderInList =  $GroupList
                    ->SummitSelectedPresentations()
                    ->sort('Order DESC')
                    ->first()
                    ->Order;

                $SelectedPresentation = new SummitSelectedPresentation();
                $SelectedPresentation->SummitSelectedPresentationListID = $GroupList->ID;
                $SelectedPresentation->PresentationID = $this->ID;
                $SelectedPresentation->MemberID = Member::currentUser()->ID;
                // Place at bottom of list
                $SelectedPresentation->Order = $HighestOrderInList + 1;
                $SelectedPresentation->write();
            }
        }
    }

    /**
     * Used by the track chair app to allow chairs to remove a presentation from a group list.
     **/

    public function removeFromGroupList() {


        // Check permissions of user on talk
        if ($this->CanAssign()) {

            $GroupList = SummitSelectedPresentationList::get()
                ->filter(array(
                    'CategoryID' => $this->CategoryID,
                    'ListType' => 'Group'
                ))
                ->first();


            // See if the presentation has already been assigned
            $AlreadyAssigned = $GroupList->SummitSelectedPresentations('PresentationID = ' . $this->ID)->first();

            if ($AlreadyAssigned->exists()) {
                $AlreadyAssigned->delete();
            }
        }
    }

    /**
     * Used by the track chair app see if the presentation has been selected by currently logged in member.
     **/

    public function isSelected() {

        $memID = Member::currentUserID();


        $selected = SummitSelectedPresentation::get()
            ->leftJoin("SummitSelectedPresentationList", "SummitSelectedPresentationList.ID = SummitSelectedPresentation.SummitSelectedPresentationListID")
            ->where("PresentationID={$this->ID} and SummitSelectedPresentation.MemberID={$memID} 
                     AND ListType='Individual'");

        if ($selected->count()) return true;

    }

    public static function getLevels()
    {
        $res  = singleton('Presentation')->dbObject('Level')->enumValues();
        $list = new ArrayList();
        foreach($res as $k => $v)
        {
            $list->add(new ArrayData(array('Level'=> $v)));
        }
        return $list;
    }

    public static function getStatusOptions()
    {
        $statuses = singleton('Presentation')->config()->status_options;
        $list = new ArrayList();
        foreach($statuses as $k => $v)
        {
            $list->add(new ArrayData(array('Status'=> $v)));
        }
        return $list;
    }

    public function getCMSFields()
    {
        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $f = parent::getCMSFields();
        $f->removeByName('TypeID');
        $f->dropdown('Level', 'Level', $this->dbObject('Level')->enumValues())
            ->dropdown('CategoryID', 'Category', PresentationCategory::get()->map('ID', 'Title'))
            ->dropdown('Status', 'Status')
            ->configure()
            ->setSource(array_combine(
                $this->config()->status_options,
                $this->config()->status_options
            ))
            ->end()
            ->listbox('Topics', 'Topics', PresentationTopic::get()->map('ID', 'Title')->toArray())
            ->configure()
            ->setMultiple(true)
            ->end()
            ->text('OtherTopic', 'Other topic')
            ->htmleditor('ProblemAddressed', 'What is the problem or use case you’re addressing in this session?')
            ->htmleditor('AttendeesExpectedLearnt', 'What should attendees expect to learn?')
            ->htmleditor('SelectionMotive', 'Why should this session be selected?')
            ->tab('Preview')
            ->literal('preview', sprintf(
                '<iframe width="%s" height="%s" frameborder="0" src="%s"></iframe>',
                '100%',
                '400',
                Director::absoluteBaseURL() . $this->PreviewLink()
            ));

        $f->addFieldToTab('Root.Main', $ddl_type = new DropdownField('TypeID', 'Event Type', SummitEventType::get()->filter
        (
            array
            (
                'SummitID' => $summit_id,
            )
        )->where(" Type ='Presentation' OR Type ='Keynotes' ")->map('ID','Type')));

        $ddl_type->setEmptyString('-- Select a Presentation Type --');

        if($this->ID > 0) {
            // speakers
            $config = new GridFieldConfig_RelationEditor(100);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $speakers = new GridField('Speakers', 'Speakers', $this->Speakers(), $config);
            $f->addFieldToTab('Root.Speakers', $speakers);
            $config->getComponentByType('GridFieldAddExistingAutocompleter')->setResultsFormat('$Name - $Member.Email')->setSearchList($this->getAllowedSpeakers());
            // moderator

            $f->addFieldToTab('Root.Speakers', $ddl_moderator = new DropdownField('ModeratorID', 'Moderator', $this->Speakers()->map('ID', 'Name')));
            $ddl_moderator->setEmptyString('-- Select a Moderator --');


            $config = GridFieldConfig_RecordEditor::create(100);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses
            (
                array
                (
                    'PresentationVideo' => 'Video',
                    'PresentationSlide' => 'Slide',
                    'PresentationLink'  => 'Link',
                )
            );
            $config->addComponent($multi_class_selector);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $gridField = new GridField('Materials', 'Materials', $this->Materials(), $config);
            $f->addFieldToTab('Root.Materials', $gridField);
        }
        return $f;
    }

    private function getAllowedSpeakers()
    {
        return PresentationSpeaker::get();
    }
    /**
     * Used by the track chair app see if the presentation has been selected by the group.
     **/

    public function isGroupSelected() {

        $memID = Member::currentUserID();


        $selected = SummitSelectedPresentation::get()
            ->leftJoin("SummitSelectedPresentationList", "SummitSelectedPresentationList.ID = SummitSelectedPresentation.SummitSelectedPresentationListID")
            ->where("PresentationID={$this->ID} AND ListType='Group'");

        if ($selected->count()) return true;

    }

    /**
     * Used by the track chair app see if the presentation has been selected by anyone at all.
     * TODO: refactor to combine with isSelected() by passing optional memberID
     **/

    public function isSelectedByAnyone() {

        $selected = SummitSelectedPresentation::get()
            ->where("PresentationID={$this->ID}");

        if ($selected->count()) return true;

    }

    /**
     * Used by the track chair app see if the presentation was moved to this category.
     **/

    public function movedToThisCategory() {
        $completedMove = $this->ChangeRequests()->filter(array(
            'NewCategoryID' => $this->CategoryID,
            'Done' => TRUE
        ));
        if ($completedMove->count()) return true;
    }

    public function SelectionStatus() {

        $Selections = SummitSelectedPresentation::get()
            ->leftJoin('SummitSelectedPresentationList','SummitSelectedPresentation.SummitSelectedPresentationListID = SummitSelectedPresentationList.ID')
            ->filter(array(
                'PresentationID' => $this->ID,
                'ListType' => 'Group'
            ));

        // Error out if a talk has more than one selection
        if($Selections && $Selections->count() > 1) user_error('There cannot be more than one instance of this talk selected. Talk ID '.$this->ID);

        $Selection = NULL;
        if ($Selections) $Selection = $Selections->first();

        // Error out if the category of presentation does not match category of selection
        if($Selection && $this->CategoryID != $Selection->SummitSelectedPresentationList()->Category()->ID)
            user_error('The selection category does not match the presentation category. Presentation ID '.$this->ID);


        If (!$Selection) {
            return 'unaccepted';
        } elseif ($Selection->Order <= $this->Category()->SessionCount) {
            return 'accepted';
        } else {
            return 'alternate';
        }
    }

    protected function validate()
    {
        $this->assignEventType();

        $valid = parent::validate();

        if (!$valid->valid()) {
            return $valid;
        }

        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);

        // validate that each speakers is assigned one time at one location
        $start_date      = $summit->convertDateFromTimeZone2UTC( $this->getStartDate());
        $end_date        = $summit->convertDateFromTimeZone2UTC( $this->getEndDate() );

        $presentation_id = $this->getIdentifier();
        $location_id     = $this->LocationID;
        $speakers_id     = array();

        $speakers        = $this->Speakers();
        foreach($speakers as $speaker)
        {
            array_push($speakers_id, $speaker->ID);
        }

        $speakers_id = implode(', ', $speakers_id);

        if(empty($start_date) || empty($end_date) || empty($speakers_id))
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
E.LocationID <> 0            AND
EXISTS
(
	SELECT PS.ID FROM Presentation_Speakers PS WHERE PresentationSpeakerID IN ($speakers_id) AND
	PresentationID = P.ID
);
SQL;


        $qty = intval(DB::query($query)->value());

        if($qty > 0)
        {
            return $valid->error('There is a speaker assigned to another presentation on that date/time range !');
        }
        return $valid;
    }


    public function getSpeakers() {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this,'Speakers');
    }

    public function getTopics() {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this,'Topics');
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
        $res = Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
        if($res) return $res;

        return
            (Member::currentUser() && Member::currentUser()->IsSpeaker($this)) ||
            Member::currentUserID() == $this->CreatorID;
    }

    /**
     * @return mixed
     * @throws EntityValidationException
     */
    public function markReceived()
    {
        $validation_result = $this->validate();

        if(!$validation_result->valid())
        {
            throw new EntityValidationException($validation_result->messageList());
        }

        if(empty($this->Title)) {
            throw new EntityValidationException('Title is Mandatory!');
        }

        if(empty($this->ShortDescription))
        {
            throw new EntityValidationException('ShortDescription is mandatory!');
        }

        if(empty($this->ProblemAddressed))
        {
            throw new EntityValidationException('ProblemAddressed is mandatory!');
        }

        if(empty($this->AttendeesExpectedLearnt))
        {
            throw new EntityValidationException('AttendeesExpectedLearnt is mandatory!');
        }

        if(empty($this->SelectionMotive))
        {
            throw new EntityValidationException('SelectionMotive is mandatory!');
        }

        if(empty($this->Level))
        {
            throw new EntityValidationException('Level is mandatory!');
        }

        $this->Status = self::STATUS_RECEIVED;

        if ($this->Progress < self::PHASE_COMPLETE) {
            $this->Progress = self::PHASE_COMPLETE;
        }


        return $this;
    }
}
