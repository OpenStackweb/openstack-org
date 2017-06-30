<?php

/**
 * Class PresentationSpeaker
 */
class PresentationSpeaker extends DataObject
    implements IPresentationSpeaker
{

    use SummitEntityMetaTagGenerator;

    private static $db = [
        'FirstName'             => 'Varchar(100)',
        'LastName'              => 'Varchar(100)',
        'Title'                 => 'Varchar(100)',
        'Bio'                   => 'HTMLText',
        'IRCHandle'             => 'Varchar',
        'TwitterName'           => 'Varchar',
        'AvailableForBureau'    => 'Boolean',
        'FundedTravel'          => 'Boolean',
        'WillingToTravel'       => 'Boolean',
        'Country'               => 'Varchar(2)',
        'BeenEmailed'           => 'Boolean',
        'WillingToPresentVideo' => 'Boolean',
        'Notes'                 => 'HTMLText',
        'CreatedFromAPI'        => 'Boolean',
        'OrgHasCloud'           => 'Boolean',
    ];

    private static $has_one = [
        'Photo'               => 'Image',
        'Member'              => 'Member',
        'RegistrationRequest' => 'SpeakerRegistrationRequest',
    ];

    private static $has_many = [
        'AreasOfExpertise'         => 'SpeakerExpertise',
        'OtherPresentationLinks'   => 'SpeakerPresentationLink',
        'TravelPreferences'        => 'SpeakerTravelPreference',
        'Languages'                => 'SpeakerLanguage',
        'PromoCodes'               => 'SpeakerSummitRegistrationPromoCode',
        'AnnouncementSummitEmails' => 'SpeakerAnnouncementSummitEmail',
        'SummitAssistances'        => 'PresentationSpeakerSummitAssistanceConfirmationRequest',
    ];

    private static $searchable_fields = [
        'Member.Email',
        'FirstName',
        'LastName',
        'Bio',
        'IRCHandle',
        'TwitterName'
    ];

    private static $indexes = [
        'FirstName' => ['type' => 'index', 'value' => 'FirstName'],
        'LastName' => ['type' => 'index', 'value' => 'LastName'],
        'FirstName_LastName' => ['type' => 'index', 'value' => 'FirstName,LastName'],
    ];

    private static $defaults = [
        'MemberID'       => 0,
        'CreatedFromAPI' => false,
    ];

    private static $belongs_many_many = [
        'Presentations' => 'Presentation',
    ];

    private static $many_many = [
        'OrganizationalRoles' => 'SpeakerOrganizationalRole',
        'ActiveInvolvements' => 'SpeakerActiveInvolvement'
    ];

    private static $summary_fields = [
        'FirstName'    => 'FirstName',
        'LastName'     => 'LastName',
        'Member.Email' => 'Email',
        'Bio'          => 'Bio',
        'IRCHandle'    => 'IRCHandle',
        'TwitterName'  => 'TwitterName',
    ];

    protected function onBeforeDelete() {
        parent::onBeforeDelete();

        if($this->Photo()->exists()) $this->Photo()->delete();
        if($this->RegistrationRequest()->exists()) $this->RegistrationRequest()->delete();

        foreach($this->AreasOfExpertise() as $e){
            $e->delete();
        }
        foreach($this->OtherPresentationLinks() as $e){
            $e->delete();
        }
        foreach($this->TravelPreferences() as $e){
            $e->delete();
        }
        foreach($this->Languages() as $e){
            $e->delete();
        }
        foreach($this->PromoCodes() as $e){
            $e->delete();
        }
        foreach($this->AnnouncementSummitEmails() as $e){
            $e->delete();
        }
        foreach($this->SummitAssistances() as $e){
            $e->delete();
        }

        $this->Presentations()->removeAll();
        $this->OrganizationalRoles()->removeAll();
        $this->ActiveInvolvements()->removeAll();

        // set moderator to zero

        DB::query("UPDATE Presentation SET ModeratorID = 0 WHERE ModeratorID = {$this->ID};");
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $this->FirstName   = trim($this->FirstName);
        $this->LastName    = trim($this->LastName);
        $this->Title       = trim($this->Title);
        $this->IRCHandle   = trim($this->IRCHandle);
        $this->TwitterName = trim($this->TwitterName);
    }
    /**
     * Gets a readable label for the speaker
     *
     * @return  string
     */
    public function getName()
    {
        $full_name = "{$this->FirstName} {$this->LastName}";
        if (empty($full_name)) {
            $full_name = $this->Member()->exists() ? $this->Member()->getFullName() : 'TBD';
        }

        return $full_name;
    }

    /**
     * Gets a url label for the speaker
     *
     * @return  string
     */

    public function getNameSlug() {
        $slug = preg_replace('/\W+/', '', $this->FirstName).'-'.preg_replace('/\W+/', '', $this->LastName);
        $slug = strtolower($slug);
        return $slug;
    }

    /**
     * Gets a url label for the speaker
     *
     * @return  string
     */

    public function getProfileLink($absolute = true) {
        $page = SpeakerListPage::get()->first();
        if ($page) {
            if($absolute)
                return $page->getAbsoluteLiveLink(false) . 'profile/' . $this->ID . '/' . $this->getNameSlug();
            return $page->RelativeLink(false) . 'profile/' . $this->ID . '/' . $this->getNameSlug();
        }
    }

    public function getCountryName()
    {
        $country = '';

        if ($this->Country) {
            $country = Geoip::countryCode2name($this->Country);
        }

        return $country;
    }

    public function getCurrentPosition()
    {
        $member = $this->Member();
        if (!is_null($member) && $member->ID > 0) {
            return $member->getCurrentPosition();
        }

        return 'N/A';
    }

    public function getTitleNice()
    {
        $title = '';
        $member = $this->Member();
        if (!is_null($member) && $member->ID > 0) {
            $title = $member->getCurrentPosition();
        }

        return (trim($title) != '') ? $title : $this->Title;
    }

    /**
     * Helper method to link to this speaker, given an action
     *
     * @param   $action
     * @return  string
     */
    protected function linkTo($presentationID, $action = null)
    {

        $presentation = Presentation::get()->byID($presentationID);
        if (is_null($presentation)) return false;
        if ($page = PresentationPage::get()->filter('SummitID', $presentation->SummitID)->first()) {
            return Controller::join_links(
                $page->Link(),
                'manage',
                $presentationID,
                'speaker',
                $this->ID,
                $action
            );
        }
    }

    /**
     * Gets a link to edit this record
     *
     * @return  string
     */
    public function EditLink($presentationID)
    {
        $action = 'edit';
        if ($this->isPendingOfRegistration()) {
            $action .= '?' . SpeakerRegistrationRequest::ConfirmationTokenParamName . '=' . $this->RegistrationRequest()->getToken();
        }
        return $this->linkTo($presentationID, $action);
    }

    /**
     * Gets a link to delete this presentation
     *
     * @return  string
     */
    public function DeleteLink($presentationID)
    {
        return $this->linkTo($presentationID, 'delete?t=' . SecurityToken::inst()->getValue());
    }

    /**
     * Gets a link to the speaker's review page, as seen in the email. Auto authenticates.
     * @param Int $presentationID
     * @return string
     */
    public function ReviewLink($presentationID)
    {
        $action = 'review';
        if ($this->isPendingOfRegistration()) {
            $action .= '?' . SpeakerRegistrationRequest::ConfirmationTokenParamName . '=' . $this->RegistrationRequest()->getToken();
        }
        return $this->linkTo($presentationID, $action);
    }

    /**
     * Gets a link to speaker bio form
     *
     * @return  string
     */
    public function BioLink()
    {
        $action = 'bio';
        if ($this->isPendingOfRegistration()) {
            $action .= '?' . SpeakerRegistrationRequest::ConfirmationTokenParamName . '=' . $this->RegistrationRequest()->getToken();
        }

        if ($page = PresentationPage::get()->filter('SummitID', Summit::get_active()->getIdentifier())->first()) {
            return Controller::join_links(
                $page->Link(),
                $action
            );
        }
    }


    public function getCMSFields()
    {
        $fields = FieldList::create(TabSet::create("Root"))
            ->text('FirstName', "Speaker's first name")
            ->text('LastName', "Speaker's last name")
            ->text('Title', "Speaker's title")
            ->htmleditor('Bio', "Speaker's Bio")
            ->text('IRCHandle', 'IRC Handle (optional)')
            ->text('TwitterName', 'Twitter Handle (optional)')
            ->imageUpload('Photo', 'Upload a speaker photo')
            ->memberAutoComplete('Member', 'Member');

        if ($this->ID > 0) {
            // presentations
            $config = GridFieldConfig_RelationEditor::create();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $gridField = new GridField('Presentations', 'Presentations', $this->Presentations(), $config);
            $fields->addFieldToTab('Root.Presentations', $gridField);

            // AreasOfExpertise
            $config = GridFieldConfig_RecordEditor::create();
            $gridField = new GridField('AreasOfExpertise', 'AreasOfExpertise', $this->AreasOfExpertise(), $config);
            $fields->addFieldToTab('Root.Main', $gridField);

            // OtherPresentationLinks
            $config = GridFieldConfig_RecordEditor::create();
            $gridField = new GridField('OtherPresentationLinks', 'OtherPresentationLinks', $this->OtherPresentationLinks(), $config);
            $fields->addFieldToTab('Root.Main', $gridField);

            // TravelPreferences
            $config = GridFieldConfig_RecordEditor::create();
            $gridField = new GridField('TravelPreferences', 'TravelPreferences', $this->TravelPreferences(), $config);
            $fields->addFieldToTab('Root.Main', $gridField);

            // Languages
            $config = GridFieldConfig_RecordEditor::create();
            $gridField = new GridField('Languages', 'Languages', $this->Languages(), $config);
            $fields->addFieldToTab('Root.Main', $gridField);

            // Summit Assistances

            $config = GridFieldConfig_RecordEditor::create();
            $gridField = new GridField('SummitAssistances', 'Summit Assistances', $this->SummitAssistances(), $config);
            $fields->addFieldToTab('Root.Main', $gridField);

        }

        return $fields;
    }

    public function AllPresentations($summit_id = null)
    {
        $summit = !$summit_id ? Summit::get_active() : Summit::get()->byID($summit_id);
        if (!$summit) return false;

        return $this->Presentations()->filter(array(
            'SummitID' => $summit->ID
        ));
    }

    public function MyPresentations($summit_id = null)
    {
        $summit = !$summit_id ? Summit::get_active() : Summit::get()->byID($summit_id);
        if (!$summit) return false;

        return Presentation::get()->filter([
            'CreatorID' => $this->MemberID,
            'SummitID' => $summit->ID
        ]);
    }

    public function AllRelatedPresentations()
    {
        $presentations = $this->Presentations()->toArray();
        $moderator_pres = Presentation::get()->filter('ModeratorID',$this->ID)->toArray();
        $all_presentations = array_merge($presentations, $moderator_pres);

        return $all_presentations;
    }

    /**
     * @param null $summit_id
     * @param string $role
     * @param bool $exclude_privates_tracks
     * @return bool|DataList
     */
    public function PublishedPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker,  $exclude_privates_tracks = true)
    {
       return $this->PublishedPresentationsByType
       (
           $summit_id,
           $role,
           [IPresentationType::Keynotes, IPresentationType::Panel, IPresentationType::Presentation, IPresentationType::LightingTalks],
           $exclude_privates_tracks
       );
    }

    public function PublishedRegularPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker){
        return $this->PublishedPresentationsByType($summit_id, $role, [IPresentationType::Keynotes, IPresentationType::Panel, IPresentationType::Presentation]);
    }

    public function PublishedLightningPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker){
        return $this->PublishedPresentationsByType($summit_id, $role, [IPresentationType::LightingTalks]);
    }

    public function hasPublishedLightningPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker)
    {
        return $this->PublishedLightningPresentations($summit_id, $role)->count() > 0;
    }

    public function PublishedPresentationsByType(
        $summit_id               = null,
        $role                    = IPresentationSpeaker::RoleSpeaker,
        array $types_slugs       = [IPresentationType::Keynotes, IPresentationType::Panel, IPresentationType::Presentation, IPresentationType::LightingTalks],
        $exclude_privates_tracks = true
    ){
        $summit = !$summit_id ? Summit::get_active() : Summit::get()->byID($summit_id);
        if (!$summit) return false;

        $types = PresentationType::get()->filter(['Type' =>  $types_slugs ,'SummitID'  => $summit->ID ] );
        if($types->count() == 0 ) return false;

        $private_tracks          = [];
        $exclude_privates_tracks = boolval($exclude_privates_tracks);

        if($exclude_privates_tracks){
            $private_track_groups = PrivatePresentationCategoryGroup::get()->filter(['SummitID' =>  $summit->ID]);
            foreach($private_track_groups as $private_track_group){
                $current_private_tracks = $private_track_group->Categories()->getIDList();
                if(count($current_private_tracks) == 0) continue;
                $private_tracks = array_merge($private_tracks, array_values($current_private_tracks));
            }
        }

        $filter_conditions = [
            'SummitID'  => $summit->ID,
            'Published' => 1,
            'TypeID'    => $types->getIDList()
        ];

        if(count($private_tracks) > 0) {
            $filter_conditions['CategoryID:ExactMatch:not'] = $private_tracks;
        }

        if($role == IPresentationSpeaker::RoleSpeaker) {
            return $this->Presentations()->filter(
                $filter_conditions
            );
        }

        $filter_conditions['ModeratorID'] = $this->ID;
        return Presentation::get()->filter(
            $filter_conditions
        );
    }

    public function OtherPresentations($summit_id = null)
    {
        $summit = !$summit_id ? Summit::get_active() : Summit::get()->byID($summit_id);
        if (!$summit) return false;

        return $this->Presentations()->filter([
            'SummitID' => $summit->ID
        ])->exclude([
                'CreatorID' => $this->MemberID,
            ]);
    }

    public function ModeratorPresentations($summit_id = null)
    {
        $summit = !$summit_id ? Summit::get_active() : Summit::get()->byID($summit_id);
        if (!$summit) return false;

        return Presentation::get()->filter([
            'ModeratorID' => $this->ID,
            'SummitID'    => $summit->ID
        ]);
    }

    public function OtherModeratorPresentations($summit_id = null)
    {
        $summit = !$summit_id ? Summit::get_active() : Summit::get()->byID($summit_id);
        if (!$summit) return false;

        return Presentation::get()->filter([
            'ModeratorID' => $this->ID,
            'SummitID'    => $summit->ID
        ])
            ->exclude([
                'CreatorID' => $this->MemberID,
            ]);
    }

    public function getPresentationsCount($summit_id = null)
    {
        $count_others = $this->OtherPresentations($summit_id)->count();
        $count_mine   = $this->MyPresentations($summit_id)->count();
        return $count_mine + $count_others;
    }

    /**
     * @param ISummit $summit
     * @return DataList
     */
    public function getPublicCategoryPresentationsBySummit(ISummit $summit){

        $categories_ids = array(0);
        foreach($summit->getPublicCategories() as $cat)
            array_push($categories_ids, $cat->ID);
        if(count($categories_ids) == 0) return null;
        $categories_ids = implode(',', $categories_ids);

        return $this->Presentations()->filter([
            'SummitID' => $summit->ID
        ])
            ->exclude(['CreatorID' => $this->MemberID])
            ->exclude(['ModeratorID' => $this->ID])
            ->where(" SummitEvent.CategoryID IN ({$categories_ids})");

    }

    /**
     * @param ISummit $summit
     * @return DataList
     */
    // NOTE: THIS ONE INCLUDES CREATED, MODERATED AND SPEAKER PRESENTATIONS
    public function getPublicCategoryOwnedPresentationsBySummit(ISummit $summit){
        $categories_ids = array(0);
        foreach($summit->getPublicCategories() as $cat)
            array_push($categories_ids, $cat->ID);

        if(count($categories_ids) == 0) return null;

        $categories_ids = implode(',', $categories_ids);

        return Presentation::get()->filter([
            'CreatorID' => $this->MemberID,
            'SummitID' => $summit->ID
        ])->where(" SummitEvent.CategoryID IN ({$categories_ids})");
    }

    /**
     * @param ISummit $summit
     * @return DataList
     */
    public function getPublicCategoryModeratedPresentationsBySummit(ISummit $summit){
        $categories_ids = array(0);
        foreach($summit->getPublicCategories() as $cat)
            array_push($categories_ids, $cat->ID);
        if(count($categories_ids) == 0) return null;
        $categories_ids = implode(',', $categories_ids);

        return Presentation::get()->filter([
            'ModeratorID' => $this->ID,
            'SummitID' => $summit->ID
        ])
            ->exclude(['CreatorID' => $this->MemberID])
            ->where(" SummitEvent.CategoryID IN ({$categories_ids})");
    }

    /**
     * @param ISummit $summit
     * @param PrivatePresentationCategoryGroup $private_group
     * @return DataList
     */
    public function getPrivateCategoryPresentationsBySummit(ISummit $summit, PrivatePresentationCategoryGroup $private_group){

        $categories_ids = array();
        foreach($private_group->Categories() as $cat)
            array_push($categories_ids, $cat->ID);
        if(count($categories_ids) == 0) return null;
        $categories_ids = implode(',', $categories_ids);

        return $this->Presentations()
            ->filter(['SummitID' => $summit->ID])
            ->exclude(['CreatorID' => $this->MemberID])
            ->exclude(['ModeratorID' => $this->ID])
            ->where(" SummitEvent.CategoryID IN ({$categories_ids})");
    }

    /**
     * @param ISummit $summit
     * @param PrivatePresentationCategoryGroup $private_group
     * @return DataList
     */
    // NOTE: THIS ONE INCLUDES CREATED, MODERATED AND SPEAKER PRESENTATIONS
    public function getPrivateCategoryOwnedPresentationsBySummit(ISummit $summit, PrivatePresentationCategoryGroup $private_group){

        $categories_ids = array();
        foreach($private_group->Categories() as $cat)
            array_push($categories_ids, $cat->ID);
        if(count($categories_ids) == 0) return null;
        $categories_ids = implode(',', $categories_ids);

        return Presentation::get()->filter([
            'CreatorID' => $this->MemberID,
            'SummitID' => $summit->ID
        ])->where(" SummitEvent.CategoryID IN ({$categories_ids})");
    }

    /**
     * @param ISummit $summit
     * @param PrivatePresentationCategoryGroup $private_group
     * @return DataList
     */
    public function getPrivateCategoryModeratedPresentationsBySummit(ISummit $summit, PrivatePresentationCategoryGroup $private_group){

        $categories_ids = array();
        foreach($private_group->Categories() as $cat)
            array_push($categories_ids, $cat->ID);
        if(count($categories_ids) == 0) return null;
        $categories_ids = implode(',', $categories_ids);

        return Presentation::get()->filter([
            'ModeratorID' => $this->ID,
            'SummitID' => $summit->ID
        ])
            ->exclude(['CreatorID' => $this->MemberID])
            ->where(" SummitEvent.CategoryID IN ({$categories_ids})");
    }

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
    public function isPendingOfRegistration()
    {
        return $this->MemberID == 0;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->MemberID > 0 ? $this->Member()->Email : $this->RegistrationRequest()->Email;
    }

    /**
     * @param ICommunityMember $member
     * @return void
     */
    public function associateMember(ICommunityMember $member)
    {
        $this->MemberID = $member->getIdentifier();
    }

    public function clearBeenEmailed()
    {
        $this->BeenEmailed = false;
        $this->write();
    }

    public function PastAcceptedPresentations($limit = 5)
    {
        $acceptedPresentations = new ArrayList();
        $presentations = $this->Presentations()->sort('Created', 'DESC');
        foreach ($presentations as $presentation) {
            if ($presentation->SelectionStatus() == IPresentation::SelectionStatus_Accepted) {
                $acceptedPresentations->push($presentation);
            }

            if ($acceptedPresentations->count() >= $limit) break;
        }

        return $acceptedPresentations;
    }

    public function PastAcceptedOrPublishedPresentations($limit = 5)
    {
        $acceptedPresentations = new ArrayList();
        $all_presentations = $this->AllRelatedPresentations();

        @usort($all_presentations, function($a, $b)
        {
            if ($a->Summit()->SummitBeginDate == $b->Summit()->SummitBeginDate) return 0;
            return (strtotime($a->Summit()->SummitBeginDate) < strtotime($b->Summit()->SummitBeginDate)) ? 1 : -1;
        });

        foreach ($all_presentations as $presentation) {
            if ($presentation->SelectionStatus() == IPresentation::SelectionStatus_Accepted || $presentation->Published) {
                $acceptedPresentations->push($presentation);
            }

            if ($acceptedPresentations->count() >= $limit) break;
        }

        return new GroupedList($acceptedPresentations);
    }

    /**
     * @param null|int $summit_id
     * @param string $role
     * @return ArrayList|bool
     */
    public function AcceptedPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker)
    {
        $acceptedPresentations = new ArrayList();
        $summit = !$summit_id ? Summit::get_active() : Summit::get()->byID($summit_id);
        if (!$summit) return false;
        $presentations = $role == IPresentationSpeaker::RoleSpeaker ?
            $this->Presentations()->filter('SummitEvent.SummitID', $summit->ID):
            Presentation::get()->filter(['SummitEvent.SummitID' => $summit->ID, 'ModeratorID' => $this->ID]);

        $presentations_hash = [];
        foreach ($presentations as $p) {
            if (isset($presentations_hash[$p->ID])) continue;
            $presentations_hash[$p->ID] = $p->ID;
            if ($p->SelectionStatus() == IPresentation::SelectionStatus_Accepted || $p->isPublished()) {
                $acceptedPresentations->push($p);
            }
        }

        return $acceptedPresentations;
    }

    /**
     * @param null|int $summit_id
     * @param string $role
     * @param bool $exclude_privates_tracks
     * @return ArrayList|bool
     */
    public function UnacceptedPresentations
    (
        $summit_id = null,
        $role = IPresentationSpeaker::RoleSpeaker,
        $exclude_privates_tracks = true
    )
    {
        $unacceptedPresentations = new ArrayList();
        $summit                  = !$summit_id ? Summit::get_active() : Summit::get()->byID($summit_id);

        if (!$summit) return false;

        $private_tracks = [];

        if($exclude_privates_tracks){
            $private_track_groups = PrivatePresentationCategoryGroup::get()->filter(['SummitID' =>  $summit->ID]);
            foreach($private_track_groups as $private_track_group){
                $current_private_tracks = $private_track_group->Categories()->getIDList();
                if(count($current_private_tracks) == 0) continue;
                $private_tracks = array_merge($private_tracks, array_values($current_private_tracks));
            }
        }

        $presentations = $role == IPresentationSpeaker::RoleSpeaker ?
            $this->Presentations()->filter('SummitEvent.SummitID', $summit->ID):
            Presentation::get()->filter(['SummitEvent.SummitID' => $summit->ID, 'ModeratorID' => $this->ID]);

        if(count($private_tracks) > 0) {
            $presentations = $presentations->filter('CategoryID:ExactMatch:not', $private_tracks);
        }

        foreach ($presentations as $p) {
            if ($p->SelectionStatus() == IPresentation::SelectionStatus_Unaccepted && !$p->isPublished()) {
                $unacceptedPresentations->push($p);
            }
        }

        return $unacceptedPresentations;
    }

    /**
     * @param null $summit_id
     * @param string $role
     * @return ArrayList|bool
     */
    public function AlternatePresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker)
    {
        $alternatePresentations = new ArrayList();
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id);
        if (is_null($summit)) return false;

        $presentations = $role == IPresentationSpeaker::RoleSpeaker ?
            $this->Presentations()->filter('SummitEvent.SummitID', $summit->ID):
            Presentation::get()->filter(['SummitEvent.SummitID' => $summit->ID, 'ModeratorID' => $this->ID]);

        foreach ($presentations as $p) {
            if ($p->SelectionStatus() == IPresentation::SelectionStatus_Alternate && !$p->isPublished()) {
                $alternatePresentations->push($p);
            }
        }
        return $alternatePresentations;
    }

    /**
     * @param int $summit_id
     * @return string
     * @throws EntityValidationException
     * @throws NotFoundEntityException
     */
    public function getSpeakerConfirmationLink($summit_id)
    {
        $confirmation_page  = SummitConfirmSpeakerPage::getBy(intval($summit_id));
        if (!$confirmation_page)
            throw new NotFoundEntityException('Confirmation Speaker Page not set on current summit!');

        $url = $confirmation_page->getAbsoluteLiveLink(false);

        if ($this->hasAssistanceFor($summit_id)) {
            throw new EntityValidationException(sprintf('this is already an assistance request for speaker %s on summit id %s',$this->ID, $summit_id));
        }

        $request = $this->createAssistanceFor($summit_id);
        $token = null;
        $already_exists = false;

        do {
            $token = $request->generateConfirmationToken();
            $already_exists = intval(PresentationSpeakerSummitAssistanceConfirmationRequest::get()
                    ->filter([
                        'SummitID'                 => intval($summit_id),
                        'SpeakerID:ExactMatch:not' => $this->ID,
                        'ConfirmationHash'         => PresentationSpeakerSummitAssistanceConfirmationRequest::HashConfirmationToken($token)
                    ])
                    ->count()) > 1;
        } while ($already_exists);

        $request->write();

        return $url . 'confirm?t=' . base64_encode($token);
    }

    /**
     * @param int $summit_id
     * @return PresentationSpeakerSummitAssistanceConfirmationRequest
     */
    public function createAssistanceFor($summit_id)
    {
        $request = PresentationSpeakerSummitAssistanceConfirmationRequest::create();
        $request->SummitID = $summit_id;
        $request->SpeakerID = $this->ID;

        return $request;
    }

    /**
     * Resets the confirmation request if exists and its not confirmed yet
     * otherwise exception
     * @param int $summit_id
     * @return string
     * @throws Exception
     * @throws ValidationException
     * @throws null
     */
    public function resetConfirmationLink($summit_id)
    {
        $confirmation_page = SummitConfirmSpeakerPage::get()->filter('SummitID', intval($summit_id))->first();
        if (!$confirmation_page) throw new NotFoundEntityException('Confirmation Speaker Page not set on current summit!');
        $url = $confirmation_page->getAbsoluteLiveLink(false);
        $request = PresentationSpeakerSummitAssistanceConfirmationRequest::get()
            ->filter([
                'SummitID' => intval($summit_id),
                'SpeakerID' => $this->ID
            ])->first();

        if (is_null($request)) {
            throw new EntityValidationException('there is not valid request!');
        }

        if ($request->alreadyConfirmed()) {
            throw new EntityValidationException('request already confirmed!');
        }

        $token = null;
        $already_exists = false;

        do {
            $token = $request->generateConfirmationToken();
            $already_exists = intval(PresentationSpeakerSummitAssistanceConfirmationRequest::get()
                    ->filter([
                        'SummitID' => intval($summit_id),
                        'SpeakerID:ExactMatch:not' => $this->ID,
                        'ConfirmationHash' => PresentationSpeakerSummitAssistanceConfirmationRequest::HashConfirmationToken($token)
                    ])
                    ->count()) > 1;
        } while ($already_exists);

        $request->write();

        return $url . 'confirm?t=' . base64_encode($token);
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function announcementEmailAlreadySent($summit_id)
    {
        $email_type = $this->getAnnouncementEmailTypeSent($summit_id);
        return !is_null($email_type) && $email_type !== 'NONE';
    }

    /**
     * @param int $summit_id
     * @return string|null
     */
    public function getAnnouncementEmailTypeSent($summit_id)
    {
        $email = $this->AnnouncementSummitEmails()
            ->filter('SummitID', $summit_id)
            ->where("AnnouncementEmailTypeSent <> 'SECOND_BREAKOUT' ")
            ->first();
        return !is_null($email) ? $email->AnnouncementEmailTypeSent : null;
    }

    /***
     * @param string $email_type
     * @param int $summit_id
     * @return $this|void
     * @throws Exception
     */
    public function registerAnnouncementEmailTypeSent($email_type, $summit_id)
    {
        if ($this->announcementEmailAlreadySent($summit_id)) {
            throw new Exception('Announcement Email already sent');
        }

        $email = SpeakerAnnouncementSummitEmail::create();
        $email->SpeakerID = $this->ID;
        $email->SummitID = $summit_id;
        $email->AnnouncementEmailTypeSent = $email_type;
        $email->AnnouncementEmailSentDate = MySQLDatabase56::nowRfc2822();
        $email->write();
    }

    /**
     * @param int $summit_id
     * @param string $role
     * @return bool
     */
    public function hasRejectedPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker)
    {
        return $this->UnacceptedPresentations($summit_id, $role)->count() > 0;
    }

    /**
     * @param int $summit_id
     * @param string $role
     * @return bool
     */
    public function hasApprovedPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker)
    {
        return $this->AcceptedPresentations($summit_id, $role)->count() > 0;
    }

    /**
     * @param int $summit_id
     ** @param string $role
     * @return bool
     */
    public function hasAlternatePresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker)
    {
        return $this->AlternatePresentations($summit_id, $role)->count() > 0;
    }

    /**
     * @param ISpeakerSummitRegistrationPromoCode $promo_code
     * @return $this
     */
    public function registerSummitPromoCode(ISpeakerSummitRegistrationPromoCode $promo_code)
    {
        $promo_code->assignSpeaker($this);
        $promo_code->write();

        return $this;
    }

    /**
     * @param $promo_code_value
     * @param ISummit $summit
     * @return ISpeakerSummitRegistrationPromoCode
     * @throws EntityValidationException
     * @throws ValidationException
     */
    public function registerSummitPromoCodeByValue($promo_code_value, ISummit $summit)
    {
        // check if we have an assigned one already
        $old_code = SpeakerSummitRegistrationPromoCode::get()->filter([
            'SummitID' => $summit->getIdentifier(),
            'SpeakerID' => $this->ID,
        ])
            ->first();

        // we are trying to update the promo code with another one ....
        if ($old_code && $promo_code_value !== $old_code->Code) {
            throw new EntityValidationException(sprintf(
                'speaker has already assigned to another registration code (%s)', $old_code->Code
            ));
        }
        //we already have the same code ...

        if ($old_code) return $old_code;

        // check if the promo code already exists and assigned to another user
        $existent_code = SpeakerSummitRegistrationPromoCode::get()->filter([
            'Code' => $promo_code_value,
            'SummitID' => $summit->getIdentifier(),
            'SpeakerID:ExactMatch:not' => 0,
        ])
            ->first();

        if ($existent_code) {
            throw new EntityValidationException(sprintf(
                'there is another speaker with that code for this summit (%s)', $promo_code_value
            ));
        }
        // check if promo code exists and its not assigned ...
        $code = SpeakerSummitRegistrationPromoCode::get()->filter([
            'Code'      => $promo_code_value,
            'SummitID'  => $summit->getIdentifier(),
            'SpeakerID' => 0,
        ])
            ->first();

        if (!$code) {
            //create it
            $code = SpeakerSummitRegistrationPromoCode::create();
            $code->SummitID = $summit->getIdentifier();
            $code->Code     = $promo_code_value;
            $code->write();
        }

        $this->registerSummitPromoCode($code);
        $code->write();

        return $code;
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasSummitPromoCode($summit_id)
    {
        $code = $this->getSummitPromoCode($summit_id);
        return !is_null($code);
    }

    /**
     * @param int $summit_id
     * @return ISpeakerSummitRegistrationPromoCode
     */
    public function getSummitPromoCode($summit_id)
    {
        return $this->PromoCodes()->filter('SummitID', $summit_id)->first();
    }

    public function ProfilePhoto($width = 100)
    {
        $generic_url = Director::absoluteBaseURL() . 'summit/images/generic-speaker-icon.png';
        $img1 = $this->Photo();
        $member = $this->Member();
        $img2 = !is_null($member) && $member->ID > 0 ? $member->Photo() : null;
        $twitter_name = $this->TwitterName;
        if (!is_null($img1) && $img1->exists() && Director::fileExists($img1->Filename)) {
            // make it square
            $size = ($img1->getWidth() < $img1->getHeight()) ? $img1->getWidth() : $img1->getHeight();
            $img1 = $img1->CroppedImage($size, $size);
            // we resize it if the photo is too large
            $img1 = (($size - $width) < 200) ? $img1 : $img1->SetRatioSize($width, $width);
            return is_null($img1) ? $generic_url : $img1->getAbsoluteURL();
        }
        if (!is_null($img2) && $img2->exists() && Director::fileExists($img2->Filename)) {
            // make it square
            $size = ($img2->getWidth() < $img2->getHeight()) ? $img2->getWidth() : $img2->getHeight();
            $img2 = $img2->CroppedImage($size, $size);
            // we resize it if the photo is too large
            $img2 = (($size - $width) < 200) ? $img2 : $img2->SetRatioSize($width, $width);
            return is_null($img2) ? $generic_url : $img2->getAbsoluteURL();
        } elseif (!empty($twitter_name)) {
            if ($width < 100)
                return 'https://twitter.com/' . trim(trim($twitter_name, '@')) . '/profile_image?size=normal';
            return 'https://twitter.com/' . trim(trim($twitter_name, '@')) . '/profile_image?size=bigger';
        }
        return $generic_url;
    }

    public function getShortBio($length = 200)
    {
        $bio = strip_tags($this->getField('Bio'));

        if (strlen($bio) <= $length) return $bio;

        $length -= 3; // account for '...'
        $pos = strrpos(substr($bio, 0, $length + 1), ' ');
        if ($pos === false) $pos = $length;
        $short_bio = substr($bio, 0, $pos) . '...';
        return $short_bio;
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
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    // Used to group speakers by last name when displaying the member listing
    public function getLastNameFirstLetter()
    {
        $firstLetter = $this->owner->LastName[0];
        $firstLetter = strtr($firstLetter,
            'ŠŽšžŸµÀÁÂÃÄÅÈÉÊËÌÍÎÏÐÑÒÓÔÕÖÙÚÛÜÝ',
            'SZszYuAAAAAAEEEEIIIIDNOOOOOUUUUY');
        $firstLetter = strtoupper($firstLetter);

        return $firstLetter;
    }

    /**
     * @param int $summit_id
     * @return string
     */
    public function getOnSitePhoneFor($summit_id)
    {
        $request = $this->getAssistanceFor($summit_id);
        return !is_null($request) ? $request->OnSitePhoneNumber : '';
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasAssistanceFor($summit_id)
    {
        return $this->SummitAssistances()->filter('SummitID', intval($summit_id))->count() > 0;
    }

    /**
     * @param int $summit_id
     * @return PresentationSpeakerSummitAssistanceConfirmationRequest
     */
    public function getAssistanceFor($summit_id)
    {
        return $this->SummitAssistances()->filter('SummitID', intval($summit_id))->first();
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function breakoutEmailAlreadySent($summit_id)
    {
        $count1 = intval($this->AnnouncementSummitEmails()->filter('SummitID', $summit_id)->filter('AnnouncementEmailTypeSent', 'SECOND_BREAKOUT_REMINDER')->count());
        $count2 = intval($this->AnnouncementSummitEmails()->filter('SummitID', $summit_id)->filter('AnnouncementEmailTypeSent', 'SECOND_BREAKOUT_REGISTER')->count());
        return $count1 > 0 || $count2 > 0;
    }

    /**
     * @return bool
     */
    public function membershipCreateEmailAlreadySent()
    {
        $email = $this->AnnouncementSummitEmails()->filter('SummitID', 0)->filter('AnnouncementEmailTypeSent', 'CREATE_MEMBERSHIP')->first();
        return !is_null($email);
    }

    /***
     * @param int $summit_id
     * @param string $type
     * @return $this|void
     * @throws Exception
     */
    public function registerBreakOutSent($summit_id, $type)
    {
        if ($this->breakoutEmailAlreadySent($summit_id)) {
            throw new Exception('Second Breakout Email already sent');
        }

        $email = SpeakerAnnouncementSummitEmail::create();
        $email->SpeakerID = $this->ID;
        $email->SummitID = $summit_id;
        $email->AnnouncementEmailTypeSent = $type;
        $email->AnnouncementEmailSentDate = MySQLDatabase56::nowRfc2822();
        $email->write();

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function registerCreateMembershipSent()
    {
        if ($this->membershipCreateEmailAlreadySent()) {
            throw new Exception('Create Membership Email already sent');
        }

        $email = SpeakerAnnouncementSummitEmail::create();
        $email->SpeakerID = $this->ID;
        $email->SummitID = 0;
        $email->AnnouncementEmailTypeSent = 'CREATE_MEMBERSHIP';
        $email->AnnouncementEmailSentDate = MySQLDatabase56::nowRfc2822();
        $email->write();

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPendingRegistrationRequest()
    {
        $request = $this->RegistrationRequest();
        return !is_null($request) && $request->exists() && !$request->alreadyConfirmed();
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasConfirmedAssistanceFor($summit_id)
    {
        $assistance = $this->getAssistanceFor($summit_id);
        return !is_null($assistance) && $assistance->alreadyConfirmed();
    }

    public function SpeakerHash()
    {
        $prefix = "000";
        $hash = base64_encode($prefix . $this->Member()->Email);

        return $hash;
    }


    // Look to see if a presenter has a general session or a keynote.
    public function GeneralOrKeynote()
    {
        $presentations = $this->PublishedPresentations();
        if (!$presentations->exists()) return false;

        foreach ($presentations as $presentation) {
            if ($presentation->event_type == 'General Session' || $presentation->event_type == 'Keynotes') return true;
            break;
        }
    }


    public static function hash_to_username($hash)
    {
        return substr(base64_decode($hash), 3);
    }

    /**
     * @param int $summit_id
     * @param string $role
     * @param bool $exclude_privates_tracks
     * @return bool
     */
    public function hasPublishedPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker,  $exclude_privates_tracks = true)
    {
        return $this->PublishedPresentations($summit_id, $role, $exclude_privates_tracks)->count() > 0;
    }

    /**
     * @param int $summit_id
     * @param string $role
     * @return bool
     */
    public function hasHadPublishedPresentations($role = IPresentationSpeaker::RoleSpeaker)
    {
        $presentations = null;
        if($role == IPresentationSpeaker::RoleSpeaker) {
            $presentations = $this->Presentations()->filter([
                'Published' => 1
            ]);
        } else {
            $presentations = Presentation::get()->filter([
                'Published' => 1,
                'ModeratorID' => $this->ID
            ]);
        }

        return $presentations->count() > 0;
    }

    /**
     * @param null|int $summit_id
     * @return mixed
     */
    public function AllPublishedPresentations($summit_id = null)
    {
        $res           = [];
        $p_speaker     = $this->PublishedPresentations($summit_id, IPresentationSpeaker::RoleSpeaker);
        $p_moderator   = $this->PublishedPresentations($summit_id, IPresentationSpeaker::RoleModerator);
        $merge         = array_merge($p_speaker->toArray(), $p_moderator->toArray());
        $already_added = [];
        foreach($merge as $p){
            if(isset($already_added[$p->ID])) continue;
            $res[]                 = $p;
            $already_added[$p->ID] = $p->ID;
        }
        return new ArrayList($res);
    }

    /**
     * @param ISummit $summit
     * @return int
     */
    public function getPublicCategoryOwnedPresentationsBySummitCount(ISummit $summit){
        $list = $this->getPublicCategoryOwnedPresentationsBySummit($summit);
        return is_null($list) ? 0 : $list->count();
    }

    /**
     * @param ISummit $summit
     * @return int
     */
    public function getPublicCategoryPresentationsBySummitCount(ISummit $summit){
        $list = $this->getPublicCategoryPresentationsBySummit($summit);
        return is_null($list) ? 0 : $list->count();
    }

    /**
     * @param ISummit $summit
     * @return int
     */
    public function getPublicCategoryModeratedPresentationsBySummitCount(ISummit $summit){
        $list = $this->getPublicCategoryModeratedPresentationsBySummit($summit);
        return is_null($list) ? 0 : $list->count();
    }

    /**
     * @param ISummit $summit
     * @param PrivatePresentationCategoryGroup $private_group
     * @return int
     */
    public function getPrivateCategoryPresentationsBySummitCount(ISummit $summit, PrivatePresentationCategoryGroup $private_group){
        $list = $this->getPrivateCategoryPresentationsBySummit($summit, $private_group);
        return is_null($list) ? 0 : intval($list->count());
    }

    /**
     * @param ISummit $summit
     * @param PrivatePresentationCategoryGroup $private_group
     * @return int
     */
    public function getPrivateCategoryOwnedPresentationsBySummitCount(ISummit $summit, PrivatePresentationCategoryGroup $private_group){
        $list = $this->getPrivateCategoryOwnedPresentationsBySummit($summit, $private_group);
        return is_null($list) ? 0 : intval($list->count());
    }

    /**
     * @param ISummit $summit
     * @param PrivatePresentationCategoryGroup $private_group
     * @return int
     */
    public function getPrivateCategoryModeratedPresentationsBySummitCount(ISummit $summit, PrivatePresentationCategoryGroup $private_group){
        $list = $this->getPrivateCategoryModeratedPresentationsBySummit($summit, $private_group);
        return is_null($list) ? 0 : intval($list->count());
    }

    /**
     * @param ISummit $summit
     * @return ArrayList
     */
    public function getFeedback(ISummit $summit){
        $presentations = $this->Presentations()->filter('SummitID',$summit->ID);
        $feedbacks = new ArrayList();
        foreach($presentations as $pres) {
            if ($pres->Feedback()->count()) {
                $feedbacks->merge($pres->Feedback()->toArray());
            }
        }

        return $feedbacks;
    }

    /**
     * @param ISummit $summit
     * @return float
     */
    public function getAvgFeedback(ISummit $summit){
        $feedbacks = $this->getFeedback($summit);
        $sum = $count = 0;

        foreach ($feedbacks as $feedback) {
            $sum += $feedback->Rate;
            $count++;
        }

        return $count ? round($sum/$count,1) : 0;
    }
}