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
                "PresentationSpeaker",
                "Moderator.ID = Presentation.ModeratorID",
                "Moderator"
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
                    OR (CONCAT_WS(' ', Moderator.FirstName, Moderator.LastName)) LIKE '%{$k}%' 
                    OR Tag.Tag = '{$k}' 
                ");
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
        'FeatureCloud'            => 'Boolean',
        'LightningTalk'           => 'Boolean',
        'ToRecord'                => 'Boolean',
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
    private static $many_many = array
    (
        'Speakers' => 'PresentationSpeaker',
        'Topics'   => 'PresentationTopic',
    );

    /**
     * @var array
     */
    static $many_many_extraFields = array(
        'Speakers' => array
        (
            'IsCheckedIn' => "Boolean",
        ),
    );

    /**
     * @var array
     */
    private static $has_one = array
    (
        'Creator'   => 'Member',
        'Moderator' => 'PresentationSpeaker',
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
     * @return bool
     */
    public function isLightningWannabe()
    {
        return boolval($this->LightningTalk);
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
            ->dropdown('Level', 'Level', $this->dbObject('Level')->enumValues())
            ->optionset('FeatureCloud','Does this talk feature an OpenStack cloud?', array(
                1 => 'Yes',
                0 => 'No'
            ))
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
            $ddl_type = new DropdownField('TypeID', 'Event Type', SummitEventType::get()->filter
            (
                array
                (
                    'SummitID' => $summit_id,
                )
            )->where(" Type ='Presentation' OR Type ='Keynotes' OR Type ='Panel' ")->map('ID', 'Type')));

        $ddl_type->setEmptyString('-- Select a Presentation Type --');

        if ($this->ID > 0) {
            // speakers
            $config = new GridFieldConfig_RelationEditor(100);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $speakers = new GridField('Speakers', 'Speakers', $this->Speakers(), $config);
            $f->addFieldToTab('Root.Speakers', $speakers);
            $config->getComponentByType('GridFieldAddExistingAutocompleter')->setResultsFormat('$Name - $Member.Email')->setSearchList($this->getAllowedSpeakers());
            // moderator

            $f->addFieldToTab('Root.Speakers',
                $ddl_moderator = new DropdownField('ModeratorID', 'Moderator', $this->Speakers()->map('ID', 'Name')));
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
                    'PresentationLink' => 'Link',
                )
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
        $result       = [];
        $moderator_id = 0;
        if($this->Moderator()->exists()) {
            $result[]     = $this->Moderator();
            $moderator_id = $this->Moderator()->ID;
        }

        if($this->Speakers()->exists()) {
			$result =  array_merge(
				$result, 
				$this->Speakers()->exclude([
					'ID' => $moderator_id
				])->toArray()
			);        	
        }
    	
    	return new ArrayList($result);
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
     * @throws Exception
     */
    public function maxSpeakersReached()
    {
        return ($this->Type()->getMaxSpeakers() == $this->Speakers()->count());
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function maxModeratorsReached()
    {
        $max_moderators  = $this->Type()->getMaxModerators();
        $moderator_count = ($this->Moderator()->exists() ? 1 : 0);
        return ($max_moderators == $moderator_count);
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
     * @return bool|PersistentCollection
     * @throws Exception
     */
    public function getTopics()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Topics');
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
            $member->ID == $this->CreatorID ||
            ( $speaker && $this->ModeratorID == $speaker->ID );
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

    public function unsetModerator(){
        $this->ModeratorID = null;
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
        return intval($this->ModeratorID) === intval($speaker_id);
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
}
