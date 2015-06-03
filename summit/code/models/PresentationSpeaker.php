<?php


class PresentationSpeaker extends DataObject
{

    private static $db = array (
        'FirstName' => 'Varchar',
        'LastName' => 'Varchar',
        'Title' => 'Varchar',
        'Bio' => 'HTMLText',
        'IRCHandle' => 'Varchar',
        'TwitterHandle' => 'Varchar',
        'AvailableForBureau' => 'Boolean',
        'FundedTravel' => 'Boolean',
        'Expertise' => 'Text',
        'Country' => 'Varchar(2)'
    );


    private static $has_one = array (
        'Photo' => 'Image',        
        'Member' => 'Member',
        'Summit' => 'Summit'
    );


    private static $indexes = array (
        //'EmailAddress' => true
    );


    private static $belongs_many_many = array (
        'SchedPresentations' => 'SchedPresentation',
        'Presentations' => 'Presentation',
    );


    /**
     * Gets a readable label for the speaker
     * 
     * @return  string
     */
    public function getName() {
        return "{$this->FirstName} {$this->LastName}";
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
     */
    public function ReviewLink($presentationID) {
        return $this->linkTo($presentationID, 'review?token='.$this->Member()->AuthenticationToken);
    }


    /**
     * Determines if the user can edit this speaker
     * 
     * @return  boolean
     */
    public function canEdit($member = null) {
        return $this->Presentation()->canEdit($member);
    }


    public function getCMSFields() {
        return FieldList::create(TabSet::create("Root"))
            ->text('FirstName',"Speaker's first name")
            ->text('LastName', "Speaker's last name")
            ->text('Title', "Speaker's title")
            ->tinyMCEEditor('Bio',"Speaker's Bio")
            ->text('IRCHandle','IRC Handle (optional)')
            ->text('TwitterHandle','Twitter Handle (optional)')
            ->imageUpload('Photo','Upload a speaker photo');
    }



    public function MyPresentations() {
        return Summit::get_active()->Presentations()->filter(array(
            'CreatorID' => $this->MemberID
        ));
    }


    public function OtherPresentations() {
        return $this->Presentations()->exclude(array(
            'CreatorID' => $this->MemberID
        ));        
    }
}