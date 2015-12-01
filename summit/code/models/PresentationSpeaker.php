<?php

/**
 * Class PresentationSpeaker
 */
class PresentationSpeaker extends DataObject
implements IPresentationSpeaker
{

    private static $db = array
    (
        'FirstName' => 'Varchar',
        'LastName' => 'Varchar',
        'Title' => 'Varchar',
        'Bio' => 'HTMLText',
        'IRCHandle' => 'Varchar',
        'TwitterHandle' => 'Varchar',
        'AvailableForBureau' => 'Boolean',
        'FundedTravel' => 'Boolean',
        'WillingToTravel' => 'Boolean',
        'Country' => 'Varchar(2)',
        'BeenEmailed' => 'Boolean',
        'ConfirmedDate' => 'SS_Datetime',
        'OnSitePhoneNumber' => 'Text',
        'RegisteredForSummit' => 'Boolean'
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
    );

    private static $searchable_fields = array
    (
        'Member.Email',
        'FirstName',
        'LastName',
        'Bio',
        'IRCHandle',
        'TwitterHandle'
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
        'TwitterHandle'             => 'TwitterHandle',
    );

    /**
     * Gets a readable label for the speaker
     * 
     * @return  string
     */
    public function getName() {
        return "{$this->FirstName} {$this->LastName}";
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
        if($page = PresentationPage::get()->first()) {
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
            ->tinyMCEEditor('Bio',"Speaker's Bio")
            ->text('IRCHandle','IRC Handle (optional)')
            ->text('TwitterHandle','Twitter Handle (optional)')
            ->imageUpload('Photo','Upload a speaker photo')
            ->memberAutoComplete('Member', 'Member');

         if($this->ID > 0)
         {
             // presentations
             $config = GridFieldConfig_RelationEditor::create();
             $config->removeComponentsByType('GridFieldAddNewButton');
             $gridField = new GridField('Presentations', 'Presentations', $this->Presentations(), $config);
             $fields->addFieldToTab('Root.Presentations', $gridField);
         }

         return $fields;
    }

    public function AllPresentations() {
        return $this->Presentations()->filter(array(
            'Status' => 'Received'
        ));    
    }

    public function MyPresentations($summit_id = null) {
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        return $summit->Presentations()->filter(array(
            'CreatorID' => $this->MemberID
        ));
    }

    public function OtherPresentations() {
        return $this->Presentations()->exclude(array(
            'CreatorID' => $this->MemberID
        ));        
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

    public function AcceptedPresentations($summit_id = null) {
        $AcceptedPresentations = new ArrayList();
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        $Presentations = $this->Presentations('`SummitID` = '.$summit->ID);
        foreach ($Presentations as $Presentation) {
            if($Presentation->SelectionStatus() == "accepted") $AcceptedPresentations->push($Presentation);
        }

        return $AcceptedPresentations;
    }

    public function UnacceptedPresentations($summit_id = null) {
        $UnacceptedPresentations = new ArrayList();
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        $Presentations = $this->Presentations('`SummitID` = '.$summit->ID);
        foreach ($Presentations as $Presentation) {
            if($Presentation->SelectionStatus() == "unaccepted") $UnacceptedPresentations->push($Presentation);
        }

        return $UnacceptedPresentations;
    }

    public function AlternatePresentations($summit_id = null) {
        $AlternatePresentations = new ArrayList();
        $summit = is_null($summit_id) ? Summit::get_active() : Summit::get()->byID($summit_id) ;
        $Presentations = $this->Presentations('`SummitID` = '.$summit->ID);
        foreach ($Presentations as $Presentation) {
            if($Presentation->SelectionStatus() == "alternate") $AlternatePresentations->push($Presentation);
        }

        return $AlternatePresentations;
    }

    public function getSpeakerConfirmHash() {
        $id = $this->ID;
        $prefix = "000";
        $hash = base64_encode($prefix . $id);
        return $hash;
    }

    public function getSpeakerConfirmationLink()
    {
        $confirmation_page = SummitConfirmSpeakerPage::get()->filter('SummitID', Summit::get_active()->ID)->first();
        if(!$confirmation_page) throw new Exception('Confirmation Speaker Page not set on current summit!');
        $url = $confirmation_page->getAbsoluteLiveLink(false);
        $url = $url.'confirm?h='.$this->getSpeakerConfirmHash();
        return $url;
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
        $twitter_name = $this->TwitterHandle;
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
                return 'https://twitter.com/'.trim($twitter_name,'@').'/profile_image?size=normal';
            } else {
                return 'https://twitter.com/'.trim($twitter_name,'@').'/profile_image?size=bigger';
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