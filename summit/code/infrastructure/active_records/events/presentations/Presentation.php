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
     * defines a phase where a presentation has a tags
     */
    const PHASE_TAGS = 2;

    /**
     * defines a phase where a presentation has a summary and speakers
     */
    const PHASE_SPEAKERS = 3;


    /**
     * Defines a phase where a presentation has been submitted successfully
     */
    const PHASE_COMPLETE = 4;

    /**
     *
     */
    const STATUS_RECEIVED = 'Received';

    /**
     * @param $progress
     * @return bool
     */
    public static function isValidProgressState($progress)
    {
        $valid = array(
            self::PHASE_NEW,
            self::PHASE_SUMMARY,
            self::PHASE_TAGS,
            self::PHASE_SPEAKERS,
            self::PHASE_COMPLETE
        );
        return in_array($progress, $valid);
    }

    public static function apply_search_query(DataList $list, $keyword)
    {
        $k = Convert::raw2sql($keyword);
        return $list
            ->leftJoin(
                "Presentation_Speakers",
                "Presentation_Speakers.PresentationID = Presentation.ID"
            )
            ->leftJoin(
                "PresentationSpeaker",
                "Speaker.ID = Presentation_Speakers.PresentationSpeakerID",
                "Speaker"
            )
            ->leftJoin(
                "SummitEvent_Tags",
                "Presentation.ID = SummitEvent_Tags.SummitEventID"
            )
            ->leftJoin(
                "Tag",
                "Tag.ID = SummitEvent_Tags.TagID"
            )
            ->where("
                  	SummitEvent.Title LIKE '%{$k}%'
                  	OR SummitEvent.Abstract LIKE '%{$k}%'
                    OR (CONCAT_WS(' ', Speaker.FirstName, Speaker.LastName)) LIKE '%{$k}%'
                    OR Tag.Tag = '{$k}'");
    }

    /**
     * @return int
     */
    public function getProgress()
    {
        return intval($this->getField('Progress'));
    }

    /**
     * @param int $progress
     * @return $this
     * @throws EntityValidationException
     */
    public function setProgress($progress)
    {
        if (!self::isValidProgressState($progress)) {
            throw new EntityValidationException('invalid presentation progress');
        }

        if ($this->getProgress() < $progress) {
            $this->setField('Progress', $progress);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setComplete()
    {
        $this->Progress = self::PHASE_COMPLETE;
        return $this;
    }

    /**
     * @var array
     */
    private static $db = array
    (
        'Level'                   => "Enum('Beginner,Intermediate,Advanced,N/A')",
        'Status'                  => 'Varchar',
        'OtherTopic'              => 'Varchar',
        'Progress'                => 'Int',
        'Views'                   => 'Int',
        'BeenEmailed'             => 'Boolean',
        'ProblemAddressed'        => 'HTMLText',
        'AttendeesExpectedLearnt' => 'HTMLText',
        'Legacy'                  => 'Boolean',
        'ToRecord'                => 'Boolean',
        'AttendingMedia'          => 'Boolean'
    );

    /**
     * @var array
     */
    private static $defaults = array
    (
        'TrackChairGivenOrder' => 0,
        'AllowFeedBack'        => 1,
        'ToRecord'             => 0
    );

    /**
     * @var array
     */
    private static $has_many = array
    (
        'Votes'          => 'PresentationVote',
        // this is related to track chairs app
        'Comments'       => 'SummitPresentationComment',
        'ChangeRequests' => 'SummitCategoryChange',
        'Materials'      => 'PresentationMaterial',
        'ExtraAnswers'   => 'TrackAnswer',
    );

    /**
     * @var array
     */
    private static $many_many = [

        'Speakers' => 'PresentationSpeaker',
        'Topics'   => 'PresentationTopic',
    ];

    /**
     * @var array
     */
    static $many_many_extraFields = [
        'Speakers' => [
            'IsCheckedIn' => "Boolean",
            "Role"        => 'Enum(array("Speaker","Moderator"), "Speaker")',
        ],
    ];

    /**
     * @var array
     */
    private static $has_one = array
    (
        'Creator'       => 'Member',
        'SelectionPlan' => 'SelectionPlan'
    );

    /**
     * @var array
     */
    private static $summary_fields = array
    (
        'Created'         => 'Created',
        'Title'           => 'Event Title',
        'Level'           => 'Level',
        'SelectionStatus' => 'Status',
    );

    /**
     *
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->TypeID)
            $this->assignEventType();
    }

    protected function onBeforeDelete()
    {
        parent::onBeforeDelete();
        foreach($this->Votes() as $e){
            $e->delete();
        }
        foreach($this->Comments() as $e){
            $e->delete();
        }
        foreach($this->ChangeRequests() as $e){
            $e->delete();
        }
        foreach($this->Materials() as $e){
            $e->delete();
        }
        $this->Topics()->removeAll();
        $this->Speakers()->removeAll();
    }

    /**
     *
     */
    public function assignEventType($type=IPresentationType::Presentation)
    {
        $summit_id = intval($this->SummitID);
        if ($summit_id > 0 && intval($this->TypeID) === 0) {
            Summit::seedBasicEventTypes($summit_id);
            $event_type = SummitEventType::get()->filter(array(
                'Type' => $type,
                'SummitID' => $summit_id
            ))->first();

            if ($event_type->Exists()) {
                $this->TypeID = $event_type->ID;
            }
        }
    }

    /**
     * @param string $type
     * @param bool $absolute
     * @return null|string
     */
    public function getLink($type ='voting', $absolute = true)
    {
        if($type=='voting'){
            $page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first();
            if(!is_null($page)) {
                return $page->Link('show/' . $this->ID);
            }
        }
        if($type=='show') {
          return parent::getLink($type, $absolute);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function hasVideos()
    {
        $videos = $this->Materials()->filter('ClassName', 'PresentationVideo');
        return ($videos->count() > 0);
    }

    /**
     * @param bool $absolute
     * @return null|string
     */
    public function getVideoLink($absolute = true)
    {
        $first_video = $this->Materials()->filter('ClassName', 'PresentationVideo')->first();

        if ($first_video) {
            return $first_video->getLink($absolute);
        }

        return null;
    }

    public function getTitleNice() {
        return ($this->Title) ? $this->Title : $this->ID;
    }

    /**
     * Gets a link to edit this presentation
     *
     * @return  string
     */
    public function EditLink()
    {
        if ($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(), 'manage', $this->ID, 'summary');
        }
    }

    /**
     * Gets a link to edit this presentation
     *
     * @return  string
     */
    public function EditTagsLink()
    {
        if ($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(), 'manage', $this->ID, 'tags');
        }
    }

    /**
     * Gets a link to edit confirmation for this presentation
     *
     * @return  string
     */
    public function EditConfirmLink()
    {
        if ($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(), 'manage', $this->ID, 'confirm');
        }
    }


    /**
     * Gets a link to the preview iframe
     *
     * @return  string
     */
    public function PreviewIFrameLink()
    {
        if ($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(), 'manage', $this->ID, 'preview_iframe');
        }
    }

    /**
     * Gets a link to the preview
     *
     * @return  string
     */
    public function PreviewLink()
    {
        if ($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(), 'preview', $this->ID);
        }
    }


    /**
     * Gets a link to edit the speakers of the presentation
     *
     * @return  string
     */
    public function EditSpeakersLink()
    {
        if ($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(), 'manage', $this->ID, 'speakers');
        }
    }

    /**
     * Gets a link to delete this presentation
     *
     * @return  string
     */
    public function DeleteLink()
    {
        if ($page = PresentationPage::get()->filter('SummitID', $this->SummitID)->first()) {
            return Controller::join_links($page->Link(), 'manage', $this->ID, 'delete',
                '?t=' . SecurityToken::inst()->getValue());
        }
    }


    /**
     * @return HTMLText
     */
    public function PreviewHTML()
    {
        $template = new SSViewer('PresentationPreview');

        return $template->process(ArrayData::create(array(
            'Presentation' => $this
        )));
    }



    /**
     * Determines if the user can create a presentation
     *
     * @return  boolean
     */
    public function canCreate($member = null)
    {
        return Member::currentUser();
    }


    /**
     * Determines if the user can delete a presentation
     *
     * @return  boolean
     */
    public function canDelete($member = null)
    {
        return Permission::check("ADMIN")
        || Permission::check("ADMIN_SUMMIT_APP")
        || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE")
        || $this->CreatorID == Member::currentUserID();
    }


    /**
     * A custom permission for removing (not deleting) speakers
     * @param  Member $member
     * @return boolean         [description]
     */
    public function canRemoveSpeakers($member = null)
    {
        return true;
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

    /**
     * @return bool
     */
    public function creatorIsSpeaker()
    {
        $c = $this->Speakers()->filter(array(
            'MemberID' => $this->CreatorID
        ));
        if ($c->count()) {
            return true;
        }
    }

    /**
     * @return mixed
     */
    public function creatorBeenEmailed()
    {
        return $this->BeenEmailed;
    }

    /**
     * @throws ValidationException
     * @throws null
     */
    public function clearBeenEmailed()
    {
        $this->BeenEmailed = false;
        $this->write();
    }


    /**
     * @return ArrayList
     */
    public static function getLevels()
    {
        $res  = singleton('Presentation')->dbObject('Level')->enumValues();
        $list = new ArrayList();
        foreach ($res as $k => $v) {
            $list->add(new ArrayData(array('Level' => $v)));
        }
        return $list;
    }

    /**
     * @return ArrayList
     */
    public static function getStatusOptions()
    {
        $statuses = singleton('Presentation')->config()->status_options;
        $list = new ArrayList();
        foreach ($statuses as $k => $v) {
            $list->add(new ArrayData(array('Status' => $v)));
        }
        return $list;
    }

    public function getStatusNice() {
        if ($this->isPublished()) {
            return 'Accepted';
        } else {
            return $this->Status;
        }
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $summit_id = isset($_REQUEST['SummitID']) ? $_REQUEST['SummitID'] : $this->SummitID;

        $f = parent::getCMSFields();
        $f->removeByName('TypeID');
        $f
            ->checkbox('ToRecord', 'To Record ?')
            ->checkbox('Attending Media', 'Available to discuss with attending media?')
            ->dropdown('Level', 'Level', $this->dbObject('Level')->enumValues())
            ->listbox('Topics', 'Topics', PresentationTopic::get()->map('ID', 'Title')->toArray())
            ->configure()
            ->setMultiple(true)
            ->end()
            ->text('OtherTopic', 'Other topic')
            ->htmleditor('AttendeesExpectedLearnt', 'What should attendees expect to learn?')
            ->tab('Preview')
            ->literal('preview', sprintf(
                '<iframe width="%s" height="%s" frameborder="0" src="%s"></iframe>',
                '100%',
                '400',
                Director::absoluteBaseURL() . $this->PreviewIFrameLink()
            ));

        $f->addFieldToTab('Root.Main',
            $ddl_type = new DropdownField('TypeID', 'Event Type', PresentationType::get()->filter
            (
              [
                  'SummitID' => $summit_id,
              ]
            )->where(" Type ='Presentation' OR Type ='Keynotes' OR Type ='Panel' ")->map('ID', 'Type')));

        $f->addFieldToTab('Root.Main', new ReadonlyField("CreatorID", "Creator (Member ID) "));

        $ddl_type->setEmptyString('-- Select a Presentation Type --');

        if ($this->ID > 0) {
            // speakers
            $config = new GridFieldConfig_RelationEditor(PHP_INT_MAX);
            $config->removeComponentsByType(new GridFieldDetailForm());
            $config->removeComponentsByType(new GridFieldDataColumns());
            $config->removeComponentsByType(new GridFieldAddNewButton());

            $edittest = new GridFieldDetailForm();
            /**
             *   'IsCheckedIn' => "Boolean",
             *   "Role"        => 'Enum(array("Speaker","Moderator"), "Speaker")',
             */
            $edittest->setFields(FieldList::create(
                new ReadonlyField('FirstName','First Name'),
                new ReadonlyField('LastName','Last Name'),
                new ReadonlyField('Email','Email'),
                new ReadonlyField('MemberID','Member ID'),
                new DropdownField('ManyMany[Role]', 'Role', [
                    'Speaker' => 'Speaker',
                    'Moderator' => 'Moderator'
                ])
            ));
            $summaryfieldsconf = new GridFieldDataColumns();
            $summaryfieldsconf->setDisplayFields(
                [ 'FirstName' => 'First Name',
                    'LastName' => 'Last Name',
                    'Email' => 'Email',
                    'Role' => 'Role',
            ]);

            $config->addComponent($edittest);
            $config->addComponent($summaryfieldsconf, new GridFieldFilterHeader());

            $speakers = new GridField('Speakers', 'Speakers', $this->Speakers(), $config);
            $f->addFieldToTab('Root.Speakers', $speakers);
            $config->getComponentByType('GridFieldAddExistingAutocompleter')->setResultsFormat('$Name - $Member.Email')->setSearchList($this->getAllowedSpeakers());
            // materials
            $config = GridFieldConfig_RecordEditor::create(100);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses
            (
               [
                    'PresentationVideo' => 'Video',
                    'PresentationSlide' => 'Slide',
                    'PresentationLink'  => 'Link',
               ]
            );
            $config->addComponent($multi_class_selector);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $gridField = new GridField('Materials', 'Materials', $this->Materials(), $config);
            $f->addFieldToTab('Root.Materials', $gridField);
        }
        return $f;
    }

    /**
     * @return DataList
     */
    private function getAllowedSpeakers()
    {
        return PresentationSpeaker::get();
    }

    public function getSpeakersAndModerators()
    {
        return $this->Speakers();
    }

    /**
     * @return bool
     */
    public function allowSpeakers()
    {
        return true;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function MaterialType($type)
    {
        $materials = $this->Materials();
        if ($materials->exists()) {
            return $materials->filter('ClassName', $type)->first();
        }

        return false;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getMaterialByType($type)
    {
        $materials = $this->Materials();
        if ($materials->exists()) {
            return $materials->filter(['ClassName' => $type, 'DisplayOnSite' => 1])->sort('Order');
        }

        return false;
    }

    /**
     * @return ValidationResult
     */
    protected function validate()
    {
        if (!$this->TypeID)
            $this->assignEventType();

        $valid = parent::validate();

        if (!$valid->valid()) {
            return $valid;
        }

        $summit_id = isset($_REQUEST['SummitID']) ? $_REQUEST['SummitID'] : $this->SummitID;
        $summit = Summit::get()->byID($summit_id);

        // validate that each speakers is assigned one time at one location
        $start_date = $summit->convertDateFromTimeZone2UTC($this->getStartDate());
        $end_date = $summit->convertDateFromTimeZone2UTC($this->getEndDate());

        $presentation_id = $this->getIdentifier();

        $location_id     = intval($this->LocationID);
        $speakers_id     = [];
        if($location_id <= 0){
            return $valid;
        }

        $speakers = $this->Speakers();
        foreach ($speakers as $speaker) {
            array_push($speakers_id, $speaker->ID);
        }

        $speakers_id = implode(', ', $speakers_id);

        if (empty($start_date) || empty($end_date) || empty($speakers_id)) {
            return $valid;
        }

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

        if ($qty > 0) {
            return $valid->error('There is a speaker assigned to another presentation on that date/time range !');
        }

        return $valid;
    }


    /**
     * @return bool|PersistentCollection
     * @throws Exception
     */
    public function getSpeakers()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Speakers');
    }

    /**
     * @return bool
     */
    public function maxSpeakersReached()
    {
        if(!$this->Type()->exists()) return false;
        return ($this->Type()->getMaxSpeakers() == $this->getSpeakersCount());
    }

    /**
     * @return int
     */
    public function getSpeakersCount():int{
        return intval($this->Speakers()->filter('Role', IPresentationSpeaker::RoleSpeaker)->count());
    }

    /**
     * @param $role
     * @return mixed
     */
    public function getSpeakersByRole($role){
        return $this->Speakers()->filter('Role', $role);
    }
    /**
     * @return bool
     */
    public function hasSpeakers():bool {
        return $this->getSpeakersCount() > 0;
    }

    public function useModerators():bool{
        if(!$this->Type()->exists()) return false;
        return $this->Type()->UseModerator;
    }

    public function UseSpeakers():bool{
        if(!$this->Type()->exists()) return false;
        return $this->Type()->UseSpeakers;
    }

    /**
     * @return bool
     */
    public function maxModeratorsReached():bool
    {
        if(!$this->Type()->exists()) return false;
        return ($this->Type()->getMaxModerators() == $this->getModeratorsCount());
    }

    /**
     * @return int
     */
    public function getModeratorsCount():int {
        return intval($this->Speakers()->filter('Role', IPresentationSpeaker::RoleModerator)->count());
    }

    /**
     * @return bool
     */
    public function hasModerators(): bool {
        return $this->getModeratorsCount() > 0;
    }

    /**
     * @return string
     */
    public function getSpeakersCSV()
    {
        return implode(', ', array_map(function ($s) {
            return $s->getName();
        }, $this->getSpeakersAndModerators()->toArray()));
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null)
    {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null)
    {
        $member = Member::currentUser();
        if(!$member) return false;
        if(Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE"))
            return true;
        $speaker = $member->getSpeakerProfile();
        return
            $member->isSpeakerOn($this) ||
            $member->ID == $this->CreatorID;
    }

    /**
     * @return mixed
     * @throws EntityValidationException
     */
    public function markReceived()
    {
        $validation_result = $this->validate();

        if (!$validation_result->valid()) {
            throw new EntityValidationException($validation_result->messageList());
        }

        if (empty($this->Title)) {
            throw new EntityValidationException('Title is Mandatory!');
        }

        if (empty($this->Abstract)) {
            throw new EntityValidationException('Abstract is mandatory!');
        }

        if (empty($this->Level)) {
            throw new EntityValidationException('Level is mandatory!');
        }

        $this->Status = self::STATUS_RECEIVED;

        $this->setComplete();

        return $this;
    }

    /**
     * @param IPresentationSpeaker $speaker
     * @return bool
     */
    public function isModerator(IPresentationSpeaker $speaker)
    {
       return $this->isModeratorByID($speaker->getIdentifier());
    }

    /**
     * @param int $speaker_id
     * @return bool
     */
    public function isModeratorByID($speaker_id)
    {
        return intval($this->Speakers()->filter(["PresentationSpeakerID" =>intval($speaker_id), "Role" => IPresentationSpeaker::RoleModerator])->count()) > 0;
    }

    /**
     * @param IPresentationSpeaker $speaker
     * @return void
     */
    public function removeSpeaker(IPresentationSpeaker $speaker)
    {
        $this->getSpeakers()->remove($speaker);
    }

    /**
     * @param ITrackQuestionTemplate $question
     * @return ITrackAnswer
     */
    public function findAnswerByQuestion(ITrackQuestionTemplate $question)
    {
        foreach ($this->ExtraAnswers() as $answer) {
            if($answer->question()->getIdentifier() === $question->getIdentifier()) {
                if (!is_null($answer)) {
                    return $answer;
                }
            }
        }

        return null;
    }

    /**
     * @return String[]
     */
    public function getWordCloud() {
        $cloud_array = [];
        $rake = new Rake();

        foreach ($this->Tags() as $tag) {
            $tag_word = strval(trim(strtolower($tag->Tag)));
            if (empty($tag_word)) continue;
            if(!isset($cloud_array[$tag_word])) $cloud_array[$tag_word] = 0;
            $cloud_array[$tag_word]++;
        }

        $title_array = $rake->extract_words($this->Title);
        foreach ($title_array as $word => $count) {
            if(!isset($cloud_array[$word])) $cloud_array[$word] = 0;
            $cloud_array[$word] += $count;
        }

        return $cloud_array;
    }

    /**
     * @return bool
     */
    public function areSpeakersMandatory(): bool
    {
        if(!$this->Type()->exists()) return false;
        $min_speakers = $this->Type()->MinSpeakers;
        return $this->UseSpeakers() && ($this->Type()->AreSpeakersMandatory || $min_speakers > 0);
    }

    /**
     * @return bool
     */
    public function areModeratorsMandatory(): bool
    {
        if(!$this->Type()->exists()) return false;
        $min_speakers = $this->Type()->MinModerators;
        return $this->useModerators() && ($this->Type()->IsModeratorMandatory || $min_speakers > 0);
    }

    /**
     * @return int
     */
    public function maxModerators(): int
    {
        if(!$this->Type()->exists()) return -1;
        return intval($this->Type()->MaxModerators);
    }

    /**
     * @return int
     */
    public function maxSpeakers(): int
    {
        if(!$this->Type()->exists()) return -1;
        return intval($this->Type()->MaxSpeakers);
    }

    /**
     * @return int
     */
    public function minModerators(): int
    {
        if(!$this->Type()->exists()) return 0;
        return intval($this->Type()->MinModerators);
    }

    /**
     * @return int
     */
    public function minSpeakers(): int
    {
        if(!$this->Type()->exists()) return 0;
        return intval($this->Type()->MinSpeakers);
    }


    /**
     * returns which role should be added next
     * @return string
     */
    public function getNextSpeakerRoleToAdd(): string
    {
        if($this->useModerators()){
            if($this->areModeratorsMandatory() && !$this->minSpeakerReachedPerRole(IPresentationSpeaker::RoleModerator))
                return IPresentationSpeaker::RoleModerator;
        }

        return IPresentationSpeaker::RoleSpeaker;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function minSpeakerReachedPerRole(string $role): bool
    {
        return $role ==  IPresentationSpeaker::RoleSpeaker ?
            $this->minSpeakers() <= $this->getSpeakersCount() :
            $this->minModerators() <= $this->getModeratorsCount();
    }

    /**
     * @param string $role
     * @return bool
     */
    public function isSpeakerRoleMandatory(string $role):bool{
        return $role == IPresentationSpeaker::RoleSpeaker  ?
            $this->areSpeakersMandatory(): $this->areModeratorsMandatory();
    }

    /**
     * @param string $role
     * @return bool
     */
    public function existsSpeakersPerRole(string $role): bool{
        return $role == IPresentationSpeaker::RoleSpeaker  ?
            $this->getSpeakersCount() > 0: $this->getModeratorsCount() > 0;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function maxSpeakerReachedPerRole(string $role):bool{
        return $role ==  IPresentationSpeaker::RoleSpeaker ?
            $this->maxSpeakersReached():
            $this->maxModeratorsReached();
    }

    /**
     * @param string $role
     * @return int
     */
    public function getMinQtyPerRole(string $role): int
    {
        return $role ==  IPresentationSpeaker::RoleSpeaker ?
            $this->minSpeakers() : $this->minModerators();
    }

    /**
     * @return array
     */
    public function getSpeakersAllowedRoles(): array
    {
        $res = [];
        if($this->useModerators()){
            $res[] = IPresentationSpeaker::RoleModerator;
        }

        if($this->UseSpeakers()){
            if(!($this->useModerators() && $this->areModeratorsMandatory() &&!$this->hasModerators()))
                $res[] =  IPresentationSpeaker::RoleSpeaker;
        }

        return $res;
     }

    /**
     * @param string $role
     * @return bool
     */
    public function hasSpeakerInRole(string $role): bool
    {
        return $role ==  IPresentationSpeaker::RoleSpeaker ?
            $this->hasSpeakers() : $this->hasModerators();
    }
}
