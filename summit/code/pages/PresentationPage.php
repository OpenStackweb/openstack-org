<?php

class PresentationPage extends SummitPage
{

    private static $db = array (
        'LegalAgreement' => 'HTMLText',
        'PresentationDeadlineText' => 'HTMLText',
    );


    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.Main', new HtmlEditorField('PresentationDeadlineText', 'Presentation Deadline Text'));
        return $fields
            ->tab('LegalAgreement')
                ->htmlEditor('LegalAgreement')
        ;
    }

    public function getPresentationDeadlineText(){
        $value = $this->getField('PresentationDeadlineText');
        if(!empty($value)){
            $value = str_replace('<p>','',$value);
            $value = str_replace('</p>','',$value);
        }
        return $value;
    }
}


/**
 * Entry point for all presentation CRUD and voting. User must be logged in
 * 
 * @author  Aaron Carlino <aaron@unclecheeseproductions.com>
 */
class PresentationPage_Controller extends SummitPage_Controller
{

    private static $allowed_actions = array (
        'show',        
        'mypresentations',
        'VoteForm',
        'handleManage',
        'vote',
        'setpassword',
        'bio',
        'BioForm',
    );


    private static $url_handlers = array (
        'manage/$PresentationID!' => 'handleManage',
    );


    private static $extensions = array (
        'MemberTokenAuthenticator'
    );

    /**
     * @var ISpeakerRegistrationRequestManager
     */
    private $speaker_registration_request_manager;

    /**
     * @return ISpeakerRegistrationRequestManager
     */
    public function getSpeakerRegistrationRequestManager(){
        return $this->speaker_registration_request_manager;
    }

    /**
     * @param ISpeakerRegistrationRequestManager $speaker_registration_request_manager
     * @return void
     */
    public function setSpeakerRegistrationRequestManager(ISpeakerRegistrationRequestManager $speaker_registration_request_manager){
        $this->speaker_registration_request_manager = $speaker_registration_request_manager;;
    }

    /**
     * @var ISpeakerRegistrationRequestRepository
     */
    private $speaker_registration_request_repository;

    /**
     * @return ISpeakerRegistrationRequestRepository
     */
    public function getSpeakerRegistrationRequestRepository(){
        return $this->speaker_registration_request_repository;
    }

    /**
     * @param ISpeakerRegistrationRequestRepository $speaker_registration_request_repository
     * @return void
     */
    public function setSpeakerRegistrationRequestRepository(ISpeakerRegistrationRequestRepository $speaker_registration_request_repository){
        $this->speaker_registration_request_repository = $speaker_registration_request_repository;;
    }

    /**
     * Check for auth tokens
     * @return mixed
     */
    public function init() {
        parent::init();
        
        if(!Summit::get_active()->isInDB()) {
            return $this->httpError(404,'There is no active summit');
        }

        /**
         * On the existing tokenauthentication system, this is a fairly trivialmatter, and I'm not so sure it's anything to navigate right now.
         * Thiswas implemented to provide the video upload people a simple API foradding videos. It's a very specific use c
         * ase, and general users shouldnot be using it. If they can and they are, then that needs to bechanged.
         */
        $result = $this->checkAuthenticationToken();

        if(!$result &&!Member::currentUser()) {
            //check if speaker registration token is present..
            $speaker_registration_token = $this->request->getVar(SpeakerRegistrationRequest::ConfirmationTokenParamName);

            if(!is_null($speaker_registration_token))
            {
                $request = $this->speaker_registration_request_repository->getByConfirmationToken($speaker_registration_token);

                if(is_null($request) || $request->alreadyConfirmed()){
                    return SummitSecurity::permission_failure($this);
                }

                // redirect to register member speaker
                $url = Controller::join_links(Director::baseURL(),'summit-login','registration');

                return $this->redirect($url.'?BackURL='. urlencode( $this->request->getURL() ).'&'.SpeakerRegistrationRequest::ConfirmationTokenParamName.'='.$speaker_registration_token);
            }

            return SummitSecurity::permission_failure($this);
        }

        $speaker = Member::currentUser()->getCurrentSpeakerProfile();
        
        if(!$speaker) {
            $speaker = PresentationSpeaker::create(array(
                'MemberID' => Member::currentUserID(),
                'SummitID' => Summit::get_active()->ID,
                'FirstName' => Member::currentUser()->FirstName,
                'LastName' => Member::currentUser()->Surname
            ));
            $speaker->write();
        }    
    }


    /**
     * Hand off presentation CRUD to a sub controller. Ensure the user
     * can write to the presentation first
     * 
     * @param   $r SS_HTTPRequest
     * @return  RequestHandler
     */
    public function handleManage(SS_HTTPRequest $r) {        
        if($r->param('PresentationID') === 'new') {
            $presentation = Presentation::create();
            $presentation->CreatorID = Member::currentUserID();
            $presentation->write();

            return $this->redirect($presentation->EditLink());
        }
        else {
            $presentation = Presentation::get()->byID($r->param('PresentationID'));
        }

        if(!$presentation) return $this->httpError(404);
        
        if($presentation->isInDB() && !$presentation->canEdit()) return $this->httpError(403, "You can't edit this presentation");

        if(!$presentation->isInDB() && !$presentation->canCreate()) return $this->httpError(403);

        $request = PresentationPage_ManageRequest::create($presentation, $this);

        return $request->handleRequest($r, DataModel::inst());
    }


    public function vote(SS_HTTPRequest $r) {
        Requirements::clear();
        return $this;
    }

    
    /**
     * Action that shows user's presentations
     * 
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function mypresentations(SS_HTTPRequest $r) {
        return array (
            'Presentations' => Member::currentUser()->Presentations()
        );        
    }


    /**
     * Action that shows the presentation details, readonly
     * 
     * @param   $r SS_HTTPRequest
     * @return  array
     */
    public function show(SS_HTTPRequest $r) {
        if(!$presentation = $this->getPresentationFromRequest()) {
            return $this->httpError(404);
        }

        return array (
            'Presentation' => $presentation
        );
    }


    public function BioForm(SS_HTTPRequest $r) {        
        $form = SpeakerForm::create(
            $this,
            "BioForm",
            FieldList::create(FormAction::create('doSaveBio','Save'))
        );
        if($data = Session::get("FormInfo.{$form->FormName()}.data")) {
            $form->loadDataFrom($data);    
        }
        else {
            $form->loadDataFrom(Member::currentUser()->getCurrentSpeakerProfile());
        }
        
        return $form;
    }


    /**
     * Gets the presentations that have been already randomised for the user,
     * ensuring the same order every time.
     * 
     * @return  DataList
     */
    public function RandomisedPresentations() {
        return Member::currentUser()->getRandomisedPresentations();
    }


    /**
     * Gets all the presentations that this user has voted on
     * 
     * @return  DataList     
     */
    public function VotedPresentations() {
        return Member::currentUser()->getVotedPresentations();
    }


    /**
     * The link to create a new presentation
     * 
     * @return  string
     */
    public function CreateLink() {
        return Controller::join_links($this->Link(), 'manage','new','edit');
    }


    /**
     * The form used to vote on a presentation
     * 
     * @return  Form
     */
    public function VoteForm() {
        return Form::create(
            $this,
            "VoteForm",
            FieldList::create(
                DropdownField::create('Vote', 'Your vote', array_combine(range(1,5), range(1,5))),
                TextareaField::create('Content', 'Content (you can paste from Word!')
                    ->addExtraClass('ckeditor'),
                HiddenField::create('PresentationID', '', $this->getPresentationFromRequest()->ID)
            ),
            FieldList::create(
                FormAction::create('doVote','Vote')
            )
        );
    }


    /**
     * Handles the form submission for voting
     * 
     * @param  array $data
     * @param  Form $form
     * @return  SSViewer
     */
    public function doVote($data, $form) {
        $presentation = $this->getPresentationFromRequest();
        $presentation->setUserVote($data['Vote']);


        Member::currentUser()->removePresentation($data['PresentationID']);

        return $this->redirect($this->Link());
    }


    /**
     * A helper method that sniffs the request for a number of parameters
     * that could contain the presentation ID
     * 
     * @return  Presentation
     */
    protected function getPresentationFromRequest() {
        $presentation = false;

        if($this->request->param('ID')) {
            $presentation = Presentation::get()->byID($this->request->param('ID'));
        }

        if(!$presentation && $this->request->requestVar('PresentationID')) {
            $presentation = Presentation::get()->byID($this->request->requestVar('PresentationID'));   
        }

        return $presentation;
    }


    public function doSaveBio($data, $form) {        
        Session::set("FormInfo.{$form->FormName()}.data", $data);         
        if(empty(strip_tags($data['Bio']))) {
            $form->addErrorMessage('Bio','Please enter a bio', 'bad');
            return $this->redirectBack();
        }

        $speaker = Member::currentUser()->getCurrentSpeakerProfile();
        $form->saveInto($speaker);
        $speaker->write();

        $form->sessionMessage('Your bio has been updated', 'good');
        
        Session::clear("FormInfo.{$form->FormName()}.data", $data); 

        return $this->redirectBack();
    }
}




/**
 * Handles all requests to update a user's presentation. Presentation
 * is loaded into the request object pre-screened for write privileges
 * 
 * @author  Aaron Carlino <aaron@unclecheeseproductions.com>
 */
class PresentationPage_ManageRequest extends RequestHandler 
{

    private static $allowed_actions = array (
        'summary',
        'delete',
        'PresentationForm',
        'AddSpeakerForm',
        'doAddSpeaker',
        'doFinishSpeaker',
        'savePresentationSummary',
        'handleSpeaker',
        'confirm',
        'success',
        'speakers',
        'preview'
    );


    private static $url_handlers = array (
        'speaker/$SpeakerID!' => 'handleSpeaker'
    ); 


    /**
     * @var  PresentationPage_Controller The parent controller
     */
    protected $parent;

    
    /**
     * @var  Presentation The Presentation object being updated
     */
    protected $presentation;


    /**
     * Constructor for the request handler
     * 
     * @param   $presentation The Presentation object being updated
     * @param   $parent The parent PresentationPage_Controller object
     */
    public function __construct(Presentation $presentation, PresentationPage_Controller $parent) {
        parent::__construct();
        $this->presentation = $presentation;
        $this->parent = $parent;  
    }


    /**
     * Helper for redirect back. Needed for validator.
     * @return SS_HTTPResponse
     */
    public function redirectBack() {
        return $this->parent->redirectBack();
    }


    /**
     * Accesses the parent controller
     * 
     * @return  PresentationPage_Controller
     */
    public function getParent() {
        return $this->parent;
    }


    /**
     * Gets the presentation
     * @return Presentation
     */
    public function getPresentation() {
        return $this->presentation;
    }


    /**
     * Determines if the controller is in "create" mode
     * 
     * @return  boolean
     */
    public function isCreating() {
        return $this->presentation->isNew();
    }


    /**
     * Creates a link to this controller
     * 
     * @param   $action
     * @return  string
     */
    public function Link($action = null) {        
        return Controller::join_links(
            $this->parent->Link(), 
            'manage', 
            $this->presentation->ID ?: "new", 
            $action
        );
    }


    /**
     * Handles requests that update speakers attached to this presentation
     * and hands them off to a sub-sub controller
     * 
     * @param   $r SS_HTTPRequest
     * @return  RequestHandler
     */
    public function handleSpeaker(SS_HTTPRequest $r) {        
        if($r->param('SpeakerID') === 'new') {
            $speaker = PresentationSpeaker::create();
        }
        else {
            $speaker = PresentationSpeaker::get()->filter(array(
                'ID' => $r->param('SpeakerID'),
                'SummitID' => Summit::get_active()->ID
            ))->first();
        }

        if(!$speaker) {             
            return $this->httpError(404,'Speaker not found');
        }

        if($speaker->isInDB() && !$speaker->Presentations()->byID($this->presentation->ID)) {                     
            return $this->httpError(403, 'That speaker is not part of this presentation');
        }

        $request = PresentationPage_ManageSpeakerRequest::create($speaker, $this);

        return $request->handleRequest($r, DataModel::inst());
    }


    /**
     * Default controller action. Forwards to the main edit page for a presentation
     * 
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function index(SS_HTTPRequest $r) {
        return $this->parent->redirect($this->Link('summary'));
    }


    /**
     * Controller action that handles the main page for creating or editing
     * a presentation
     * 
     * @param   $r SS_HTTPRequest
     * @return SSViewer
     */
    public function summary(SS_HTTPRequest $r) {
        return $this->customise(array(
            'Presentation' => $this->presentation
        ))->renderWith(array('PresentationPage_summary','PresentationPage'), $this->parent);
    }


    /**
     * Controller action that handles the list of speakers for the presentation
     * 
     * @param   $r SS_HTTPRequest
     * @return SSViewer
     */
    public function speakers(SS_HTTPRequest $r) {        
        return $this->customise(array(
            'Presentation' => $this->presentation
        ))->renderWith(array('PresentationPage_speakers','PresentationPage'), $this->parent);
    }


    /**
     * Handles the deletion of a presentation
     * 
     * @param   $r SS_HTTPRequest
     * @return SSViewer
     */
    public function delete(SS_HTTPRequest $r) {
        if(!SecurityToken::inst()->check($r->getVar('t'))) {
            return $this->httpError(403, "Security token doesn't match.");
        }

        if($this->presentation->canDelete()) {
            $this->presentation->delete();

            return $this->parent->redirect($this->parent->Link());
        }

        return $this->httpError(403, 'You cannot delete this presentation');
    }


    /**
     * Controller action that handles the "confirm" page
     * 
     * @param   $r SS_HTTPRequest
     * @return SSViewer
     */     
    public function confirm(SS_HTTPRequest $r) {
        return $this->customise(array(
            'SuccessLink' => $this->Link('success'),
            'Presentation' => $this->presentation
        ))->renderWith(array('PresentationPage_confirm','PresentationPage'), $this->parent);
    }


    /**
     * Generates a preview iframe of the presentation
     * @param  SS_HTTPRequest $r 
     * @return SSViewer
     */
    public function preview(SS_HTTPRequest $r) {
        return $this->customise(array(
            'Presentation' => $this->presentation
        ))->renderWith('PresentationPage_previewiframe');
    }


    /**
     * Controller action that handles the "success" page
     *
     * @param   $r SS_HTTPRequest
     * @return SSViewer
     */     
    public function success(SS_HTTPRequest $r) {
        $speakers = $this->presentation->Speakers()->exclude(array(
            'MemberID' => $this->presentation->CreatorID
        ));

        $this->presentation->Status = 'Received';
        $this->presentation->write();

        foreach($speakers as $speaker) {

            $e = Email::create()
                ->setTo($speaker->getEmail())
                ->setUserTemplate('presentation-speaker-notification')
                ->populateTemplate(array(
                    'RecipientMember' => $speaker->Member(),
                    'Presentation'    => $this->presentation,
                    'Speaker'         => $speaker,
                    'Creator' => $this->presentation->Creator(),
                    'EditLink'      => Director::makeRelative($speaker->EditLink($this->presentation->ID)),
                    'ReviewLink'      => Director::makeRelative($speaker->ReviewLink($this->presentation->ID)),
                    'PasswordLink' => Director::absoluteBaseURL().'summit-login/lostpassword',
                    'Link' => Director::absoluteBaseURL().Director::makeRelative($this->presentation->EditLink()),
                ))
                ->send();        
        }

        // Email the creator
        Email::create()
            ->setTo($this->presentation->Creator()->Email)
            ->setUserTemplate('presentation-creator-notification')
            ->populateTemplate(array(
                'Creator' => $this->presentation->Creator(),
                'Summit' => $this->presentation->Summit(),
                'Link' => Director::absoluteBaseURL().Director::makeRelative($this->presentation->EditLink()),
                'PasswordLink' => Director::absoluteBaseURL().'summit-login/lostpassword'
            ))
            ->send();
        
        if($this->presentation->Progress < Presentation::PHASE_COMPLETE) {
            $this->presentation->Progress = Presentation::PHASE_COMPLETE;
            $this->presentation->write();
        }

        return $this->renderWith(array('PresentationPage_success','PresentationPage'), $this->parent);
    }


    /**
     * Creates the presentation add/edit form
     * 
     * @return  PresentationForm
     */
    public function PresentationForm() {
        $save = $this->presentation->isInDB() ? 'Save presentation details' : 'Save and continue <i class="fa fa-arrow-right fa-end"></i>'; 
        $form = PresentationForm::create(
            $this, 
            "PresentationForm",
            FieldList::create(
                FormAction::create('savePresentationSummary', $save)
            )
        );
        
        if($data = Session::get("FormInfo.{$form->FormName()}.data")) {            
            return $form->loadDataFrom($data);
        }

        // ugh...
        if($this->presentation->OtherTopic && !$this->presentation->CategoryID) {
        	$this->presentation->CategoryID = 'other';
        }

        return $form->loadDataFrom($this->presentation);
        
    }


    /**
     * Creates the speaker add/edit form
     * 
     * @return  Form
     */
    public function AddSpeakerForm() {

        $fields = FieldList::create (
            LiteralField::create('SpeakerNote','<p class="at-least-one">Each presentation needs at least one speaker.</p>'),
            OptionsetField::create('SpeakerType','', array(
                    'Me'   => 'Add yourself as a speaker to this presentation',
                    'Else' => 'Add someone else'
            ))->setValue('Me'),
            LiteralField::create('LegalMe','
            <div id="legal-me" style="display: none;">
             <label>
                Speakers agree that OpenStack Foundation may record and publish their talks presented during the October 2015 OpenStack Summit. If you submit a proposal on behalf of a speaker, you represent to OpenStack Foundation that you have the authority to submit the proposal on the speaker’s behalf and agree to the recording and publication of their presentation.
            </label>
            </div>'),
            EmailField::create('EmailAddress',"To add another person as a speaker, you will need their email adddress. (*)")
                ->displayIf('SpeakerType')
                    ->isEqualTo('Else')
                ->end(),
            LiteralField::create('LegalOther','
            <div id="legal-other" style="display: none;">
             <label>
                Speakers agree that OpenStack Foundation may record and publish their talks presented during the October 2015 OpenStack Summit. If you submit a proposal on behalf of a speaker, you represent to OpenStack Foundation that you have the authority to submit the proposal on the speaker’s behalf and agree to the recording and publication of their presentation.
            </label>
            </div>')

        );

        $validator = RequiredFields::create();

        if($this->presentation->Speakers()->exists()) {
            $fields->insertBefore(
                LiteralField::create('MoreSpeakers','<h3 class="more-speakers">Any more speakers to add?</h3>'),
                'SpeakerNote'
            );
            $fields->removeField('SpeakerNote');
            $actions = FieldList::create (
                FormAction::create('doAddSpeaker','<i class="fa fa-plus fa-start"></i> Add another speaker'),
                FormAction::create('doFinishSpeaker','Done adding speakers <i class="fa fa-arrow-right fa-end"></i>')
            );
            if(Member::currentUser()->IsSpeaker($this->presentation)) {
                $fields->replaceField('SpeakerType', HiddenField::create('SpeakerType','', 'Else'));
                $fields->field('EmailAddress')
                    ->setTitle('Enter the email address of your next speaker (*)')
                    ->setDisplayLogicCriteria(null);

            }
        }
        else {
            $actions = FieldList::create (
                FormAction::create('doAddSpeaker','<i class="fa fa-plus fa-start"></i> Add first speaker')                
            );            
        }



        return BootstrapForm::create (
            $this,
            "AddSpeakerForm",
            $fields,
            $actions,
            $validator
        );
    }


    /**
     * Handles the form submission for saving the first step of the presentation
     * create or edit
     * 
     * @param   $data array
     * @param   $form Form
     * @return  SSViewer
     */
    public function savePresentationSummary($data, $form) {

        Session::set("FormInfo.{$form->FormName()}.data", $data); 
        
        // This should never happen
        if(!isset($data['CategoryID'])) {
            return $this->redirectBack();
        }   
        
        if(!$data['CategoryID'] && !$data['OtherTopic']) {            
            $form->addErrorMessage('CategoryID','Please choose a topic from the list, or specify a custom topic in the Other Topic field.','bad');
            return $this->redirectBack();
        }
        if($data['CategoryID'] == 'other' && !$data['OtherTopic']) {
            $form->addErrorMessage('OtherTopic','Please specify a topic.','bad');
            return $this->redirectBack();
        }

        $new = $this->presentation->isNew();
        $form->saveInto($this->presentation);

        if($new) {
            $this->presentation->CreatorID = Member::currentUserID();
        }
 
        // assign this presentation to current summit
        $currentSummit = Summit::get_active();
        if($currentSummit) {
            $this->presentation->SummitID = $currentSummit->ID;
        }

        $this->presentation->Progress = Presentation::PHASE_SUMMARY;
        $this->presentation->write();

        Session::clear("FormInfo.{$form->FormName()}.data");
        
        if($new) {
            return $this->parent->redirect($this->Link('speakers'));    
        }

        $form->sessionMessage('Your changes have been saved.', 'good');
        
        return $this->redirectBack();
    }


    private function getSpeaker($email){
        $speaker = PresentationSpeaker::get()
            ->filter(array(
                'Member.Email' => $email,
                'SummitID' => Summit::get_active()->ID
            ))->first();
        if(is_null($speaker)){
            $speaker = PresentationSpeaker::get()
                ->filter(array(
                    'RegistrationRequest.Email' => $email,
                    'RegistrationRequest.IsConfirmed' => false,
                    'SummitID' => Summit::get_active()->ID
                ))->first();
        }
        return $speaker;
    }
    /**
     * Handles the form submission that creates a new speaker.
     * Checks for existence, and uses existing if found
     * 
     * @param   $data array
     * @param   $form Form
     * @return  SSViewer
     */
    public function doAddSpeaker($data, $form) {        
        $me = $data['SpeakerType'] == 'Me';
        $email = $me ? Member::currentUser()->Email : $data['EmailAddress'];
        
        if(!$email) {
            $form->sessionMessage('Please specify an email addresss', 'bad');
            return $this->redirectBack();
        }

        $speaker = $this->getSpeaker($email);

        if(!$speaker) {

            $speaker = PresentationSpeaker::create(array(
                'SummitID' => Summit::get_active()->ID
            ));

            if($me) {
                $member = Member::currentUser();
                $speaker->MemberID = $member->ID;
            }
            else {
                // look for the member..
                $member = Member::get()->filter('Email', $email)->first();
                if(!$member) {
                    $speaker->MemberID = 0;
                    $speaker->write();
                    $request = $this->getParent()->getSpeakerRegistrationRequestManager()->register($speaker, $email);
                    $speaker->RegistrationRequestID = $request->getIdentifier();
                }
                else {
                    $member->addToGroupByCode('speakers');
                    $speaker->MemberID = $member->ID;
                    $member->write();
                }
            }
        }

        $speaker->Presentations()->add($this->presentation->ID);
        $speaker->write();

        return $this->parent->redirect(Controller::join_links(
            $this->Link(),
            'speaker',
            $speaker->ID
        ));
    }


    /**
     * Handles the form submission for completing the "Add speakers" module
     * 
     * @param   $data array
     * @param   $form Form
     */
    public function doFinishSpeaker($data, $form) {
        if($this->presentation->Progress < Presentation::PHASE_SUMMARY) {
            return $this->parent->redirect($this->Link());
        }
        else if($this->presentation->Progress < Presentation::PHASE_SPEAKERS) {
            $this->presentation->Progress = Presentation::PHASE_SPEAKERS;
            $this->presentation->write();
        }
        
        return $this->parent->redirect($this->Link('confirm'));
    }
}


/**
 * Handles all requests to update speakers on a presentation. Speaker
 * is loaded into the request object pre-screened for write privileges
 * and ensures the speaker is part of the parent Presentation request handler
 * 
 * @author  Aaron Carlino <aaron@unclecheeseproductions.com>
 */
class PresentationPage_ManageSpeakerRequest extends RequestHandler
{
 
    private static $allowed_actions = array (
        'EditSpeakerForm',
        'BureauForm',
        'LegalForm',
        'ReviewForm',
        'doSaveBureau',
        'doSkipBureau',
        'doSaveSpeaker',
        'doSaveLegal',
        'doReviewForm',
        'edit',
        'delete',
        'bureau',
        'legal',
        'review'
    );


    /**
     * @var  Speaker The speaker being updated
     */
    protected $speaker;

    
    /**
     * @var  PresentationPage_ManageRequest The parent controller
     */
    protected $parent;

    
    /**
     * Constructor for the request
     * 
     * @param   $speaker PresentationSpeaker The speaker being updated
     * @param   $parent PresentationPage_ManageRequest The parent controller
     */
    public function __construct(PresentationSpeaker $speaker, PresentationPage_ManageRequest $parent) {
        parent::__construct();
        $this->speaker = $speaker;
        $this->parent = $parent;        
    }


    /**
     * Helper for redirect back. Needed for validator.
     * @return SS_HTTPResponse
     */
    public function redirectBack() {
        return $this->parent->getParent()->redirectBack();
    }


    /**
     * Creates a link to this controller
     * 
     * @param   $action
     * @return  string
     */
    public function Link($action = null) {
        return Controller::join_links(
            $this->parent->Link(),
            'speaker',
            $this->speaker->ID ?: "new",
            $action
        );
    }


    /**
     * Default action for the controller. Forwards to an edit view
     * 
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function index(SS_HTTPRequest $r) {        
        return $this->parent->getParent()->redirect($this->Link('edit'));
    }


    /**
     * Controller action that handles editing or creation of a speaker
     * 
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function edit(SS_HTTPRequest $r) {
        return $this->customise(array(
            'Speaker' => $this->speaker
        ))->renderWith(array('PresentationPage_editspeaker','PresentationPage'), $this->parent->getParent());
    }


    /**
     * Controller action that handles deletion of a speaker
     * 
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function delete(SS_HTTPRequest $r) {
        if(!SecurityToken::inst()->check($r->getVar('t'))) {
            return $this->httpError(403, "Security token doesn't match.");
        }

        $p = $this->parent->getPresentation();
        if($p->canRemoveSpeakers()) {
            $p->Speakers()->remove($this->speaker);
            return $this->parent->getParent()->redirect($this->parent->Link('speakers'));
        }

        return $this->httpError(403, 'You cannot remove speakers from this presentation');
    }


    /**
     * Controller action that handles the "update my details" page for a speaker
     * that has been assigned to a presentation
     * @param  SS_HTTPRequest $r 
     * @return SSViewer
     */
    public function review(SS_HTTPRequest $r) {

        if(Member::currentUserID() != $this->speaker->MemberID) {
            return $this->httpError(403, sprintf('You are logged as Member %s, but this belongs to another speaker, please log out, and try it again.', Member::currentUser()->Email));
        }

        return $this->customise(array(
            'Presentation' => $this->presentation
        ))->renderWith(array('PresentationPage_review','PresentationPage'), $this->parent->getParent());

    }


    /**
     * Controller action that handles the "speaker's bureau" module
     * 
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function bureau(SS_HTTPRequest $r) {        
        return $this->renderWith(array('PresentationPage_bureau','PresentationPage'), $this->parent->getParent());
    }


    /**
     * Controller action that sends the user to the legal agreement for video
     * @param  SS_HTTPRequest $r 
     * @return SSViewer
     */
    public function legal(SS_HTTPRequest $r) {
        return $this->renderWith(array(
    		'PresentationPage_legal',
    		'PresentationPage'
        ), $this->parent->getParent());
    }


    /**
     * Creates an edit/create form for the speakers attached to a presentation
     * 
     * @return  SpeakerForm
     */
    public function EditSpeakerForm() { 
        if(!$this->speaker->FirstName) {
            $this->speaker->FirstName = $this->speaker->Member()->FirstName;
        }
        if(!$this->speaker->Surname) {
            $this->speaker->Surname = $this->speaker->Member()->Surname;
        }

        $fields = FieldList::create();
        if($this->speaker->MemberID > 0) {
            /*if (!$this->speaker->Member()->getSummitState('VIDEO_AGREEMENT_SEEN')) {
                $fields->push(HeaderField::create('Do you agree to be video recorded?'));
                $fields->push(LiteralField::create('legal', $this->parent->getParent()->LegalAgreement));
                $fields->merge($this->LegalForm()->Fields());
            }*/
            if (!$this->speaker->Member()->getSummitState('BUREAU_SEEN')) {
                $fields->push(HeaderField::create('Want to be in the Speakers\' Bureau?'));
                $fields->merge($this->BureauForm()->Fields());
            }
        }

        $speaker_form =  SpeakerForm::create(
            $this,
            "EditSpeakerForm",
            FieldList::create (
                FormAction::create('doSaveSpeaker','Save speaker details')
            )
        )
        ->loadDataFrom($this->speaker);
        if($fields->count() > 0){
            $old_fields = $speaker_form->Fields();
            $old_fields->merge($fields);
            $speaker_form->setFields($old_fields);
        }
        return $speaker_form;
    }


    /**
     * Creates an edit/create form for the speaker's bureau questionnaire
     * 
     * @return  BureauForm
     */
    public function BureauForm() {        
        return BootstrapForm::create (
            $this,
            "BureauForm",
            FieldList::create()
                ->checkbox('AvailableForBureau',"I'd like to be in the speaker bureau")
                ->configure()
                    ->addExtraClass('bureau-checkbox')
                ->end()
                ->checkbox('FundedTravel','My company would be willing to fund my travel to events')
                ->configure()
                    ->addExtraClass('bureau-checkbox')
                ->end()
                ->countryDropdown('Country')
                ->textarea('Expertise', 'My areas of expertise (one per line)')                
            ,
            FieldList::create(
                FormAction::create('doSaveBureau','Save Preferences'),
                FormAction::create('doSkipBureau','Skip this step')
            )
        );
    }


    /**
     * Creates the form that allows a speaker to consent to being on video
     *
     * @return  Form
     */
    public function LegalForm() {
        return BootstrapForm::create (
            $this,
            "LegalForm",
            FieldList::create()
            	->literal('termbox','<div class="termbox">'.$this->parent->getParent()->LegalAgreement.'</div>')
            	->checkbox('VideoAgreement','I agree to the terms above')
            , FieldList::create(
                FormAction::create('doSaveLegal','Continue')
            )
        );
    }


    /**
     * Creates the form that lets a speaker review his details. Merges
     * several forms together.
     *
     * @return  Form
     */
    public function ReviewForm() {
        if(!$this->isMe()) return $this->httpError(403);

        $fields = FieldList::create(HeaderField::create('Your details'));
        $dummy  = SpeakerForm::create($this, "EditSpeakerForm", FieldList::create());
        $fields->merge(
            $dummy->Fields()
        );
        $fields->bootstrapIgnore('Photo');
        /*if(!$this->speaker->Member()->getSummitState('VIDEO_AGREEMENT_SEEN')) {
            $fields->push(HeaderField::create('Do you agree to be video recorded?'));
            $fields->push(LiteralField::create('legal', $this->parent->getParent()->LegalAgreement));
            $fields->merge($this->LegalForm()->Fields());
        }*/
        if(!$this->speaker->Member()->getSummitState('BUREAU_SEEN')) {
            $fields->push(HeaderField::create('Want to be in the Speakers\' Bureau?'));
            $fields->merge($this->BureauForm()->Fields());            
        }

        $form = BootstrapForm::create(
            $this,
            "ReviewForm",
            $fields,
            FieldList::create(
                FormAction::create('doReviewForm','Save my details')
            ),
            $dummy->getSpeakerValidator()
        );
        if($data = Session::get("FormInfo.{$form->FormName()}.data")) {            
            return $form->loadDataFrom($data);
        }

        return $form->loadDataFrom($this->speaker);
    }


    /**
     * Processes the legal form
     * @param  array $data
     * @param  Form $form     
     */
    public function doSaveLegal($data, $form) {
        $form->saveInto($this->speaker);
        if(!$data['VideoAgreement']) {
        	$form->sessionMessage('You must agree to the terms','bad');
        	return $this->redirectBack();
        }
        
        $this->speaker->Member()->setSummitState('VIDEO_AGREEMENT_AGREED', $this->parent->getParent()->LegalAgreement);            

        return $this->parent->getParent()->redirect($this->parent->Link('speakers'));        
    }


    /**
     * Handles the form submissions for creating/editing a speaker
     * 
     * @param   $data array
     * @param   $form Form
     * @return  SSViewer
     */
    public function doSaveSpeaker($data, $form) {
        $form->saveInto($this->speaker);
        $this->speaker->write();
        $member = $this->speaker->Member();
        if( ($member->ID > 0 && $member->getSummitState('BUREAU_SEEN')) || !$this->isMe()) {
            return $this->parent->getParent()->redirect($this->parent->Link('speakers'));        
        }

        return $this->parent->getParent()->redirect($this->Link('bureau'));
    }


    /**
     * Handles the form submissions for the bureau questionnaire
     * 
     * @param   $data array
     * @param   $form Form
     * @return  SSViewer
     */
    public function doSaveBureau($data, $form) {
        $form->saveInto($this->speaker);
        $this->speaker->Member()->setSummitState('BUREAU_SEEN');
        $this->speaker->write();

        if($this->isMe()) {
            return $this->parent->getParent()->redirect($this->Link('legal'));    
        }

        return $this->parent->getParent()->redirect($this->parent->Link('speakers'));
    }


    /**
     * Handles the form submission for skipping the questionnaire.
     * Updates the speaker record to reflect that the speaker has been asked about
     * the bureau
     * 
     * @param   $data array
     * @param   $form Form
     * @return  SSViewer
     */
    public function doSkipBureau($data, $form) {        
        $this->speaker->Member()->setSummitState('BUREAU_SEEN');        

        if(Member::currentUserID() == $this->speaker->MemberID) {
            return $this->parent->getParent()->redirect($this->Link('legal'));    
        }

        return $this->parent->getParent()->redirect($this->parent->Link('speakers'));
    }


    /**
     * Handles the form submission for the speaker "Update my details" page
     * @param  array $data 
     * @param  Form $form 
     * @return [type]       [description]
     */
    public function doReviewForm($data, $form) {
        Session::set("FormInfo.{$form->FormName()}.data", $data); 

        if(empty(strip_tags($data['Bio']))) {
            $form->addErrorMessage('Bio','Please enter a bio', 'bad');
            return $this->redirectBack();
        }
        $form->saveInto($this->speaker);
        $this->speaker->Member()->setSummitState('BUREAU_SEEEN');
        if($data['VideoAgreement'] == 1) {            
            $this->speaker->Member()->setSummitState(
                'VIDEO_AGREEMENT_AGREED', 
                $this->parent->getParent()->LegalAgreement
            );            
        }
        else {
            $this->speaker->Member()->setSummitState('VIDEO_AGREEMENT_DECLINED');
        }

        $this->speaker->write();
        $form->sessionMessage('Your details have been updated.', 'good');        
        Session::clear("FormInfo.{$form->FormName()}.data", $data); 
        
        return $this->parent->getParent()->redirectBack();
    }


    /**
     * Returns true if the current speaker is associated with the logged in user
     * @return boolean 
     */
    protected function isMe() {
        return $this->speaker->MemberID == Member::currentUserID();
    }
}
