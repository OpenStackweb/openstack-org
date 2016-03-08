<?php

/**
 * Class PresentationSpeaker
 */
class PresentationSpeaker extends DataObject
implements IPresentationSpeaker
{

    private static $db = array
    (
        'FirstName'             => 'Varchar',
        'LastName'              => 'Varchar',
        'Title'                 => 'Varchar',
        'Bio'                   => 'HTMLText',
        'IRCHandle'             => 'Varchar',
        'TwitterName'           => 'Varchar',
        'AvailableForBureau'    => 'Boolean',
        'FundedTravel'          => 'Boolean',
        'WillingToTravel'       => 'Boolean',
        'Country'               => 'Varchar(2)',
        'BeenEmailed'           => 'Boolean',
        'WillingToPresentVideo' => 'Boolean',
        'Notes'                 => 'HTMLText'
    );

    private static $has_one = array
    (
        'Photo'               => 'Image',
        'Member'              => 'Member',
        'RegistrationRequest' => 'SpeakerRegistrationRequest',
    );

    private static $has_many = array
    (
        'AreasOfExpertise'         => 'SpeakerExpertise',
        'OtherPresentationLinks'   => 'SpeakerPresentationLink',
        'TravelPreferences'        => 'SpeakerTravelPreference',
        'Languages'                => 'SpeakerLanguage',
        'PromoCodes'               => 'SpeakerSummitRegistrationPromoCode',
        'AnnouncementSummitEmails' => 'SpeakerAnnouncementSummitEmail',
        'SummitAssistances'        => 'PresentationSpeakerSummitAssistanceConfirmationRequest',
    );

    private static $searchable_fields = array
    (
        'Member.Email',
        'FirstName',
        'LastName',
        'Bio',
        'IRCHandle',
        'TwitterName'
    );

    private static $indexes = array
    (
        //'EmailAddress' => true
    );

    private static $defaults = array(
        'MemberID' => 0,
    );

    private static $belongs_many_many = array
    (
        'Presentations' => 'Presentation',
    );

    private static $summary_fields = array
    (
        'FirstName'                 => 'FirstName',
        'LastName'                  => 'LastName',
        'Member.Email'              => 'Email',
        'Bio'                       => 'Bio',
        'IRCHandle'                 => 'IRCHandle',
        'TwitterName'               => 'TwitterName',
    );

    /**
     * Gets a readable label for the speaker
     * 
     * @return  string
     */
    public function getName() {
        return "{$this->FirstName} {$this->LastName}";
    }

    public function getCountryName() {
        $country = '';

        if ($this->Country) {
            $country = Geoip::countryCode2name($this->Country);
        }

        return $country;
    }

    public function getCurrentPosition(){
        $member = $this->Member();
        if(!is_null($member) && $member->ID > 0)
        {
            return $member->getCurrentPosition();
        }
        return 'N/A';
    }

    /**
     * Helper method to link to this speaker, given an action
     * 
     * @param   $action
     * @return  string
     */
    protected function linkTo($presentationID, $action = null) {

        $presentation = Presentation::get()->byID($presentationID);
        if(is_null($presentation)) return false;
        if($page = PresentationPage::get()->filter('SummitID', $presentation->SummitID)->first()) {
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
    public function EditLink($presentationID) {
        return $this->linkTo($presentationID, 'edit');
    }

    /**
     * Gets a link to delete this presentation
     * 
     * @return  string
     */
    public function DeleteLink($presentationID) {
        return $this->linkTo($presentationID, 'delete?t='.SecurityToken::inst()->getValue());
    }

    /**
     * Gets a link to the speaker's review page, as seen in the email. Auto authenticates.
     * @param Int $presentationID
     * @return string
     */
    public function ReviewLink($presentationID) {
        $action = 'review';
        if($this->isPendingOfRegistration()){
            $action .= '?'.SpeakerRegistrationRequest::ConfirmationTokenParamName.'='.$this->RegistrationRequest()->getToken();
        }
        return $this->linkTo($presentationID, $action);
    }

     public function getCMSFields() {
        $fields =  FieldList::create(TabSet::create("Root"))
            ->text('FirstName',"Speaker's first name")
            ->text('LastName', "Speaker's last name")
            ->text('Title', "Speaker's title")
            ->htmleditor('Bio',"Speaker's Bio")
            ->text('IRCHandle','IRC Handle (optional)')
            ->text('TwitterName','Twitter Handle (optional)')
            ->imageUpload('Photo','Upload a speaker photo')
            ->memberAutoComplete('Member', 'Member');

         if($this->ID > 0)
         {
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

    public function AllPresentations($summit_id = null) {
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        if(is_null($summit)) return false;
        return $this->Presentations()->filter(array(
            'Status'   => 'Received',
            'SummitID' => $summit->ID
        ));    
    }

    public function MyPresentations($summit_id = null) {
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        if(is_null($summit)) return false;
        return Presentation::get()->filter(array(
            'CreatorID' => $this->MemberID,
            'SummitID'  => $summit->ID
        ));
    }

    public function PublishedPresentations($summit_id = null)
    {
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        if(is_null($summit)) return false;
        return $this->Presentations()->filter(array(
            'SummitID'  => $summit->ID,
            'Published' => 1
        ));
    }

    public function OtherPresentations($summit_id = null) {
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        if(is_null($summit)) return false;
        return $this->Presentations()->filter
        (
            array
            (
                'SummitID'  => $summit->ID
            )
        )->exclude
        (
            array
            (
                'CreatorID' => $this->MemberID,
            )
        );
    }

    public function getPresentationsCount($summit_id = null)
    {
       $count_others = intval($this->OtherPresentations($summit_id)->count());
       $count_mine   = intval($this->MyPresentations($summit_id)->count());
       return  $count_mine + $count_others;
    }

    public function hasReachPresentationLimitBy($summit_id = null)
    {
        if(!$this->exists()) return false;
        return ($this->getPresentationsCount($summit_id) >= MAX_SUMMIT_ALLOWED_PER_USER);
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
        return $this->MemberID == 0 ;
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
        $this->MemberID              = $member->getIdentifier();
        //$this->RegistrationRequestID = 0;
    }

    public function clearBeenEmailed() {
        $this->BeenEmailed = false;
        $this->write();
    }

    public function PastAcceptedPresentations($limit = 5) {
        $AcceptedPresentations = new ArrayList();
        $Presentations = $this->Presentations()->sort('Created','DESC')->limit($limit);
        foreach ($Presentations as $Presentation) {
            if($Presentation->SelectionStatus() == IPresentation::SelectionStatus_Accepted)
                $AcceptedPresentations->push($Presentation);
        }

        return $AcceptedPresentations;
    }

    public function AcceptedPresentations($summit_id = null) {
        $AcceptedPresentations = new ArrayList();
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        if(is_null($summit)) return false;
        $presentations = $this->Presentations()->filter('SummitEvent.SummitID',$summit->ID);

        $presentations_hash = array();
        foreach ($presentations as $p) {
            if(isset($presentations_hash[$p->ID])) continue;
            $presentations_hash[$p->ID] = $p->ID;
            if($p->SelectionStatus() == IPresentation::SelectionStatus_Accepted || $p->isPublished()) $AcceptedPresentations->push($p);
        }

        return $AcceptedPresentations;
    }

    public function UnacceptedPresentations($summit_id = null) {
        $UnacceptedPresentations = new ArrayList();
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        if(is_null($summit)) return false;
        $presentations = $this->Presentations()->filter('SummitEvent.SummitID',$summit->ID);
        foreach ($presentations as $p) {
            if($p->SelectionStatus() == IPresentation::SelectionStatus_Unaccepted && !$p->isPublished()) $UnacceptedPresentations->push($p);
        }

        return $UnacceptedPresentations;
    }

    public function AlternatePresentations($summit_id = null) {
        $AlternatePresentations = new ArrayList();
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        if(is_null($summit)) return false;
        $presentations = $this->Presentations()->filter('SummitEvent.SummitID',$summit->ID);
        foreach ($presentations as $p) {
            if($p->SelectionStatus() == IPresentation::SelectionStatus_Alternate && !$p->isPublished()) $AlternatePresentations->push($p);
        }
        return $AlternatePresentations;
    }

    /**
     * @param int $summit_id
     * @return string
     * @throws Exception
     * @throws ValidationException
     */
    public function getSpeakerConfirmationLink($summit_id)
    {
        $confirmation_page = SummitConfirmSpeakerPage::get()->filter('SummitID', intval($summit_id))->first();
        if(!$confirmation_page) throw new Exception('Confirmation Speaker Page not set on current summit!');
        $url = $confirmation_page->getAbsoluteLiveLink(false);
        $request = PresentationSpeakerSummitAssistanceConfirmationRequest::get()
            ->filter
            (
                array
                (
                    'SummitID'  => intval($summit_id),
                    'SpeakerID' => $this->ID
                )
            )->first();

        if(!is_null($request))
        {
            throw new ValidationException('there is already a confirmed request!');
        }

        $request = PresentationSpeakerSummitAssistanceConfirmationRequest::create();
        $request->SummitID  = intval($summit_id);
        $request->SpeakerID = $this->ID;
        $token              = null;
        $already_exists     = false;
        do {
            $token = $request->generateConfirmationToken();
            $already_exists =  intval(PresentationSpeakerSummitAssistanceConfirmationRequest::get()
                ->filter
                (
                    array
                    (
                        'SummitID'                 => intval($summit_id),
                        'SpeakerID:ExactMatch:not' => $this->ID,
                        'ConfirmationHash'         =>  PresentationSpeakerSummitAssistanceConfirmationRequest::HashConfirmationToken($token)
                    )
                )
                ->count()) > 1;
        } while($already_exists);
        $request->write();
        return $url.'confirm?t='.base64_encode($token);
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
       $email = $this->AnnouncementSummitEmails()->filter('SummitID', $summit_id)->first();
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
        if($this->announcementEmailAlreadySent($summit_id)) throw new Exception('Announcement Email already sent');
        $email = SpeakerAnnouncementSummitEmail::create();
        $email->SpeakerID = $this->ID;
        $email->SummitID  = $summit_id;
        $email->AnnouncementEmailTypeSent = $email_type;
        $email->AnnouncementEmailSentDate = MySQLDatabase56::nowRfc2822();
        $email->write();
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasRejectedPresentations($summit_id = null)
    {
        return $this->UnacceptedPresentations($summit_id)->count() > 0;
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasApprovedPresentations($summit_id = null)
    {
        return $this->AcceptedPresentations($summit_id)->count() > 0;
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasAlternatePresentations($summit_id = null)
    {
        return $this->AlternatePresentations($summit_id)->count() > 0;
    }

    /**
     * @param ISpeakerSummitRegistrationPromoCode $promo_code
     * @return $this
     */
    public function registerSummitPromoCode(ISpeakerSummitRegistrationPromoCode $promo_code)
    {
        $member = AssociationFactory::getInstance()->getMany2OneAssociation($this,'Member')->getTarget();
        $member->registerPromoCode($promo_code);
        $promo_code->assignSpeaker($this);
        AssociationFactory::getInstance()->getOne2ManyAssociation($this,'PromoCodes')->add($promo_code);
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
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::equal('SummitID', $summit_id));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this,'PromoCodes', $query)->first();
    }

    function ProfilePhoto($width=100){
        $img1     = $this->Photo();
        $member   = $this->Member();
        $img2     = !is_null($member) && $member->ID > 0 ? $member->Photo(): null;
        $twitter_name = $this->TwitterName;
        if(!is_null($img1)  && $img1->exists() && Director::fileExists($img1->Filename))
        {
            $img1 = $img1->SetWidth($width);
            return $img1->getAbsoluteURL();
        }
        if(!is_null($img2)  && $img2->exists() && Director::fileExists($img2->Filename))
        {
            $img2 = $img2->SetWidth($width);
            return $img2->getAbsoluteURL();
        }
        elseif (!empty($twitter_name)) {
            if ($width < 100) {
                return 'https://twitter.com/'.trim(trim($twitter_name,'@')).'/profile_image?size=normal';
            } else {
                return 'https://twitter.com/'.trim(trim($twitter_name,'@')).'/profile_image?size=bigger';
            }
        } else {
            return Director::absoluteBaseURL().'summit/images/generic-speaker-icon.png';
        }
    }

    public function getShortBio($length = 200){
        $bio = strip_tags($this->getField('Bio'));

        if (strlen($bio) < $length) return $bio;

        $pos=strpos($bio, ' ', $length);
        $short_bio = substr($bio,0,$pos ).'...';
        return $short_bio;
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
}