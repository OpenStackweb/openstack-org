<?php


class Presentation extends DataObject
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


    private static $db = array (
        'Title' => 'Text',
        'Level' => "Enum('Beginner,Intermediate,Advanced')",
        'Status' => 'Varchar',
        'OtherTopic' => 'Varchar',
        'Description' => 'HTMLText',
        'ShortDescription' => 'HTMLText',
        'Progress' => 'Int',
        'Views' => 'Int'
    );


    private static $has_many = array (
        'Votes' => 'PresentationVote',
    );


    private static $many_many = array (
        'Speakers' => 'PresentationSpeaker',        
        'Tags' => 'Tag',
        'Topics' => 'PresentationTopic'
    );


    private static $has_one = array (
        'Creator' => 'Member',
        'Category' => 'PresentationCategory',
        'Summit' => 'Summit'        
    );
    
    private static $summary_fields = array(
        'Created',
        'Title',
        'Level'
    );    

    public function getCMSFields() {
        return FieldList::create(TabSet::create('Root'))
            ->text('Title')
            ->dropdown('Level','Level', $this->dbObject('Level')->enumValues())
            ->dropdown('CategoryID','Category', PresentationCategory::get()->map('ID','Title'))
            ->dropdown('Status','Status')
                ->configure()
                    ->setSource(array_combine(
                        $this->config()->status_options,
                        $this->config()->status_options
                    ))
                ->end()
            ->listbox('Topics','Topics', PresentationTopic::get()->map('ID','Title')->toArray())
                ->configure()
                    ->setMultiple(true)
                ->end()
            ->tag('Tags', 'Tags', null, 'Presentation')
            ->text('OtherTopic','Other topic')
            ->htmlEditor('Description')
            ->htmlEditor('ShortDescription')
            ->tab('Preview')
                ->literal('preview', sprintf(
                    '<iframe width="%s" height="%s" frameborder="0" src="%s"></iframe>',
                    '100%',
                    '400',
                    Director::absoluteBaseURL().$this->PreviewLink()
                ))

            ;

    }


    /**
     * Gets a link to the presentation
     * 
     * @return  string
     */
    public function Link() {
        return PresentationPage::get()->first()->Link('show/'.$this->ID);
    }


    /**
     * Gets a link to edit this presentation
     * 
     * @return  string
     */
    public function EditLink() {
        if($page = PresentationPage::get()->first()) {
            return Controller::join_links($page->Link(),'manage', $this->ID, 'summary');
        }
    }


    /**
     * Gets a link to the preview iframe
     *
     * @return  string
     */
    public function PreviewLink() {
        if($page = PresentationPage::get()->first()) {
            return Controller::join_links($page->Link(),'manage', $this->ID, 'preview');
        }        
    }


    /**
     * Gets a link to edit the speakers of the presentation
     *
     * @return  string
     */
    public function EditSpeakersLink() {
        if($page = PresentationPage::get()->first()) {
            return Controller::join_links($page->Link(),'manage', $this->ID, 'speakers');
        }        
    }


    /**
     * Gets a link to delete this presentation
     * 
     * @return  string
     */
    public function DeleteLink() {
        if($page = PresentationPage::get()->first()) {
            return Controller::join_links($page->Link(),'manage', $this->ID, 'delete','?t='.SecurityToken::inst()->getValue());
        }
    }


    public function PreviewHTML() {
        $template = new SSViewer('PresentationPreview');

        return $template->process(ArrayData::create(array(
            'Presentation' => $this
        )));
    }


    /**
     * Determines if the user can edit this presentation
     *
     * @return  boolean
     */
    public function canEdit($member = null) {
        if(Permission::check('ADMIN')) return true;

        return  
                (Member::currentUser() && Member::currentUser()->IsSpeaker($this)) ||
                Member::currentUserID() == $this->CreatorID;
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
     * Determines if the user can create a presentation
     *
     * @return  boolean
     */
    public function canDelete($member = null) {
        return $this->CreatorID == Member::currentUserID();
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
    public function getUserVote() {
        return $this->Votes()->filter(array(
            'MemberID' => Member::currentUserID()
        ))->first();
    }


    /**
     * Determines if the presentation is "new." Since presentations are
     * optimistically written to the database, a simple isInDB() check
     * is not sufficient
     * 	
     * @return boolean
     */
    public function isNew() {
    	return $this->Progress == self::PHASE_NEW;
    }

}