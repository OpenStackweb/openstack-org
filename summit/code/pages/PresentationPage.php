<?php

/**
 * Class PresentationPage
 * Call for Speakers
 */
class PresentationPage extends SummitPage
{

    private static $db = array
    (
        'LegalAgreement'           => 'HTMLText',
        'PresentationDeadlineText' => 'HTMLText',
        'VideoLegalConsent'        => 'HTMLText',
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.Main', new HtmlEditorField('PresentationDeadlineText', 'Presentation Deadline Text'));
        $fields->addFieldsToTab('Root.LegalAgreement', new HtmlEditorField('LegalAgreement', 'Legal Agreement'));
        $fields->addFieldsToTab('Root.VideoLegalConsent', new HtmlEditorField('VideoLegalConsent', 'Video Legal Consent'));

        return $fields;
    }

    public function getPresentationDeadlineText()
    {
        $value = $this->getField('PresentationDeadlineText');
        if (!empty($value)) {
            $value = str_replace('<p>', '', $value);
            $value = str_replace('</p>', '', $value);
        }

        return $value;
    }
}


/**
 * Entry point for all presentation CRUD and voting. User must be logged in
 * @author  Aaron Carlino <aaron@unclecheeseproductions.com>
 */
class PresentationPage_Controller extends SummitPage_Controller
{

    private static $allowed_actions = array(
        'show',
        'mypresentations',
        'VoteForm',
        'handleManage',
        'vote',
        'setpassword',
        'bio',
        'BioForm',
        'preview',
        'getGoBackStep'
    );

    private static $url_handlers = array(
        'manage/$PresentationID!' => 'handleManage',
        'preview/$PresentationID!' => 'preview'
    );

    private static $extensions = array(
        'MemberTokenAuthenticator'
    );

    /**
     * @var ISpeakerRegistrationRequestManager
     */
    private $speaker_registration_request_manager;

    /**
     * @var IPresentationManager
     */
    private $presentation_manager;

    /**
     * @return ISpeakerRegistrationRequestManager
     */
    public function getSpeakerRegistrationRequestManager()
    {
        return $this->speaker_registration_request_manager;
    }

    /**
     * @return IPresentationManager
     */
    public function getPresentationManager()
    {
        return $this->presentation_manager;
    }

    /**
     * @param IPresentationManager $presentation_manager
     * @return void
     */
    public function setPresentationManager(IPresentationManager $presentation_manager)
    {
        $this->presentation_manager = $presentation_manager;
    }

    /**
     * @param ISpeakerRegistrationRequestManager $speaker_registration_request_manager
     * @return void
     */
    public function setSpeakerRegistrationRequestManager(
        ISpeakerRegistrationRequestManager $speaker_registration_request_manager
    ) {
        $this->speaker_registration_request_manager = $speaker_registration_request_manager;;
    }

    /**
     * @var ISpeakerRegistrationRequestRepository
     */
    private $speaker_registration_request_repository;

    /**
     * @return ISpeakerRegistrationRequestRepository
     */
    public function getSpeakerRegistrationRequestRepository()
    {
        return $this->speaker_registration_request_repository;
    }

    /**
     * @param ISpeakerRegistrationRequestRepository $speaker_registration_request_repository
     * @return void
     */
    public function setSpeakerRegistrationRequestRepository(
        ISpeakerRegistrationRequestRepository $speaker_registration_request_repository
    ) {
        $this->speaker_registration_request_repository = $speaker_registration_request_repository;;
    }

    /**
     * @var ISpeakerManager
     */
    private $speaker_manager;

    /**
     * @return ISpeakerManager
     */
    public function getSpeakerManager()
    {
        return $this->speaker_manager;
    }

    /**
     * @param ISpeakerManager $speaker_manager
     * @return void
     */
    public function setSpeakerManager(ISpeakerManager $speaker_manager)
    {
        $this->speaker_manager = $speaker_manager;
    }

    /**
     * @var ISpeakerRepository
     */
    private $speaker_repository;

    /**
     * @return ISpeakerRepository
     */
    public function getSpeakerRepository()
    {
        return $this->speaker_repository;
    }

    /**
     * @param ISpeakerRepository $speaker_repository
     * @return void
     */
    public function setSpeakerRepository(ISpeakerRepository $speaker_repository)
    {
        $this->speaker_repository = $speaker_repository;
    }

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    /**
     * @return IMemberRepository
     */
    public function getMemberRepository()
    {
        return $this->member_repository;
    }

    /**
     * @param IMemberRepository $member_repository
     * @return void
     */
    public function setMemberRepository(IMemberRepository $member_repository)
    {
        $this->member_repository = $member_repository;
    }

    /**
     * Check for auth tokens
     * @return mixed
     */
    public function init()
    {
        parent::init();

        Requirements::javascript('node_modules/clipboard/dist/clipboard.min.js');

        if (!Summit::get_active()->isInDB()) {
            return $this->httpError(404, 'There is no active summit');
        }

        /**
         * On the existing tokenauthentication system, this is a fairly trivialmatter, and I'm not so sure it's anything to navigate right now.
         * Thiswas implemented to provide the video upload people a simple API foradding videos. It's a very specific use c
         * ase, and general users shouldnot be using it. If they can and they are, then that needs to bechanged.
         */
        $result = $this->checkAuthenticationToken();

        if (!$result && !Member::currentUser()) {
            //check if speaker registration token is present..
            $speaker_registration_token = $this->request->getVar(SpeakerRegistrationRequest::ConfirmationTokenParamName);

            if (!is_null($speaker_registration_token)) {
                // send it to SummitSecurity Controller to complete speaker registration
                $request = $this->speaker_registration_request_repository->getByConfirmationToken($speaker_registration_token);

                if (is_null($request) || $request->alreadyConfirmed()) {
                    return $this->httpError(404);
                }

                // redirect to register member speaker
                $url = Controller::join_links(Director::baseURL(), 'summit-login', 'registration');
                Session::set(SpeakerRegistrationRequest::ConfirmationTokenParamName, $speaker_registration_token);
                Session::set('BackURL', $this->request->getURL());
                return $this->redirect($url);
            }

            return SummitSecurity::permission_failure($this);
        }

        $this->speaker_manager->ensureSpeakerProfile(Member::currentUser());

    }

    // template helper methods

    public function isCallForSpeakerOpen(){
        return $this->presentation_manager->isCallForSpeakerOpen($this->Summit(), Member::currentUser()->getSpeakerProfile());
    }

    public function isPresentationSubmissionAllowed(){
        return $this->presentation_manager->isPresentationSubmissionAllowedFor(Member::currentUser()->getSpeakerProfile(), $this->Summit());
    }

    public function canEditPresentation($presentation_id){
        return $this->presentation_manager->canEditPresentation($presentation_id, Member::currentUser()->getSpeakerProfile());
    }

    /**
     * Hand off presentation CRUD to a sub controller. Ensure the user
     * can write to the presentation first
     * @param   $r SS_HTTPRequest
     * @return  RequestHandler
     */
    public function handleManage(SS_HTTPRequest $r)
    {

        $summit = $this->Summit();

        if (is_null($summit) ||
            !$summit->exists()||
            !$this->presentation_manager->isPresentationEditionAllowed(Member::currentUser(), $summit)
        )
        {
            $r->setUrl($this->Link());//clean up dir parts so we could redirect without 404
            return $this->redirect($this->Link(), 301);
        }

        $presentation_id = Convert::raw2sql($r->param('PresentationID'));

        $presentation = $presentation_id=== 'new' ?
            Presentation::create() :
            Presentation::get()->byID($presentation_id);

        if (!$presentation) {
            return $this->httpError(404);
        }

        if ($presentation->isInDB() && !$this->canEditPresentation($presentation->ID)) {
            return $this->httpError(403, "You can't edit this presentation");
        }

        if (!$presentation->isInDB() && !$presentation->canCreate()) {
            return $this->httpError(403);
        }

        $request = PresentationPage_ManageRequest::create($presentation, $this);

        return $request->handleRequest($r, DataModel::inst());
    }

    public function vote(SS_HTTPRequest $r)
    {
        Requirements::clear();
        return $this;
    }

    /**
     * Action that shows user's presentations
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function mypresentations(SS_HTTPRequest $r)
    {
        return array(
            'Presentations' => Member::currentUser()->Presentations()
        );
    }

    /**
     * Generates a preview of the presentation
     * @param  SS_HTTPRequest $r
     * @return SSViewer
     */
    public function preview(SS_HTTPRequest $r)
    {
        if (!$presentation = $this->getPresentationFromRequest()) {
            return $this->httpError(404);
        }

        return $this->customise(array(
            'Presentation' => $presentation
        ))->renderWith(array('PresentationPage_preview', 'PresentationPage'));
    }


    /**
     * Action that shows the presentation details, readonly
     * @param   $r SS_HTTPRequest
     * @return  array
     */
    public function show(SS_HTTPRequest $r)
    {
        if (!$presentation = $this->getPresentationFromRequest()) {
            return $this->httpError(404);
        }

        return array(
            'Presentation' => $presentation
        );
    }

    public function BioForm(SS_HTTPRequest $r)
    {
        Requirements::css("themes/openstack/css/chosen.css");
        Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");

        $form = SpeakerForm::create(
            $this,
            "BioForm",
            FieldList::create(FormAction::create('doSaveBio', 'Save')),
            $this->Summit()
        );

        // add affiliations to my speaker
        $form->Fields()->insertAfter(new AffiliationField('Affiliations', 'Affiliations'), 'Photo');

        if ($data = Session::get("FormInfo.{$form->FormName()}.data")) {
            $form->loadDataFrom($data);
        } else {
            $form->loadDataFrom(Member::currentUser()->getSpeakerProfile());
        }
        return $form;
    }

    public function doSaveBio($data, $form)
    {
        Session::set("FormInfo.{$form->FormName()}.data", $data);
        if (empty(strip_tags($data['Bio']))) {
            $form->addErrorMessage('Bio', 'Please enter a bio', 'bad');

            return $this->redirectBack();
        }

        $speaker = Member::currentUser()->getSpeakerProfile();
        $form->saveInto($speaker);
        $speaker->write();

        $form->sessionMessage('Your bio has been updated', 'good');

        Session::clear("FormInfo.{$form->FormName()}.data", $data);

        return $this->redirectBack();
    }

    /**
     * Gets the presentations that have been already randomised for the user,
     * ensuring the same order every time.
     * @return  DataList
     */
    public function RandomisedPresentations()
    {
        return Member::currentUser()->getRandomisedPresentations();
    }

    /**
     * Gets all the presentations that this user has voted on
     * @return  DataList
     */
    public function VotedPresentations()
    {
        return Member::currentUser()->getVotedPresentations();
    }

    /**
     * The link to create a new presentation
     * @return  string
     */
    public function CreateLink()
    {
        return Controller::join_links($this->Link(), 'manage', 'new', 'edit');
    }

    /**
     * The form used to vote on a presentation
     * @return  Form
     */
    public function VoteForm()
    {
        return Form::create(
            $this,
            "VoteForm",
            FieldList::create(
                DropdownField::create('Vote', 'Your vote', array_combine(range(1, 5), range(1, 5))),
                TextareaField::create('Content', 'Content (you can paste from Word!')
                    ->addExtraClass('ckeditor'),
                HiddenField::create('PresentationID', '', $this->getPresentationFromRequest()->ID)
            ),
            FieldList::create(
                FormAction::create('doVote', 'Vote')
            )
        );
    }


    /**
     * Handles the form submission for voting
     * @param  array $data
     * @param  Form $form
     * @return  SSViewer
     */
    public function doVote($data, $form)
    {
        $this->presentation_manager->voteFor($this->getPresentationFromRequest(), Member::currentUser(), $data['Vote']);
        return $this->redirect($this->Link());
    }


    /**
     * A helper method that sniffs the request for a number of parameters
     * that could contain the presentation ID
     * @return  Presentation
     */
    protected function getPresentationFromRequest()
    {
        $presentation = false;

        if ($this->request->param('ID')) {
            $presentation = Presentation::get()->byID($this->request->param('ID'));
        }

        if (!$presentation && $this->request->requestVar('PresentationID')) {
            $presentation = Presentation::get()->byID($this->request->requestVar('PresentationID'));
        }

        return $presentation;
    }

    public function getGoBackStep()
    {
        return $this->getRequest()->getVar('GoBackStep');
    }

    public function getStepClass($current_step, $this_step, $progress) {
        if ($current_step == $this_step) {
            return 'current';
        } else if ($progress >= $this_step) {
            return 'completed';
        } else {
            return 'future';
        }
    }

    public function getStepClassIcon($current_step, $this_step, $progress) {
        if ($current_step == $this_step) {
            return 'fa-pencil navigation-icon-current';
        } else if ($progress >= $this_step) {
            return 'fa-check-circle navigation-icon-completed';
        } else {
            return 'fa-plus-circle navigation-icon-incompleted';
        }
    }
}


/**
 * Handles all requests to update a user's presentation. Presentation
 * is loaded into the request object pre-screened for write privileges
 * @author  Aaron Carlino <aaron@unclecheeseproductions.com>
 */
class PresentationPage_ManageRequest extends RequestHandler
{

    use RestfulJsonApiResponses;

    private static $allowed_actions = array(
        'summary',
        'tags',
        'delete',
        'PresentationForm',
        'PresentationTagsForm',
        'AddSpeakerForm',
        'doAddSpeaker',
        'doFinishSpeaker',
        'savePresentationSummary',
        'savePresentationTags',
        'handleSpeaker',
        'confirm',
        'success',
        'speakers',
        'preview_iframe',
        'searchSpeaker',
     );


    private static $url_handlers = array
    (
        'speakers/search'     => 'searchSpeaker',
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
     * PresentationPage_ManageRequest constructor.
     * @param Presentation $presentation
     * @param PresentationPage_Controller $parent
     */
    public function __construct(Presentation $presentation, PresentationPage_Controller $parent)
    {
        parent::__construct();

        Requirements::css('summit/css/call-for-presentations.css');

        $this->presentation = $presentation;
        $this->parent       = $parent;
    }

    /**
     * @return Summit
     */
    public function Summit()
    {
        return $this->parent->Summit();
    }

    /**
     * Helper for redirect back. Needed for validator.
     * @return SS_HTTPResponse
     */
    public function redirectBack()
    {
        return $this->parent->redirectBack();
    }

    /**
     * Accesses the parent controller
     * @return  PresentationPage_Controller
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Gets the presentation
     * @return Presentation
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Determines if the controller is in "create" mode
     * @return  boolean
     */
    public function isCreating()
    {
        return $this->presentation->isNew();
    }

    /**
     * Creates a link to this controller
     * @param   $action
     * @return  string
     */
    public function Link($action = null)
    {
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
     * @param   $r SS_HTTPRequest
     * @return  RequestHandler
     */
    public function handleSpeaker(SS_HTTPRequest $r)
    {
        if ($r->param('SpeakerID') === 'new') {
            $speaker = PresentationSpeaker::create();
        } else {
            $speaker = PresentationSpeaker::get()->filter(array(
                'ID' => $r->param('SpeakerID'),
            ))->first();
        }

        if (!$speaker) {
            return $this->httpError(404, 'Speaker not found');
        }

        if ($speaker->isInDB() &&
           (!$speaker->Presentations()->byID($this->presentation->ID) && $this->presentation->ModeratorID != $speaker->ID)) {
            return $this->httpError(403, 'That speaker is not part of this presentation');
        }

        $request = PresentationPage_ManageSpeakerRequest::create($speaker, $this->presentation, $this);

        return $request->handleRequest($r, DataModel::inst());
    }

    public function searchSpeaker(SS_HTTPRequest $request){

        if(!$this->checkOwnAjaxRequest())
            return $this->forbiddenError();

        if(!Member::currentUser())
            return $this->permissionFailure();

        $term = Convert::raw2sql($request->getVar('term'));
        $data = $this->parent->getSpeakerManager()->getSpeakerByTerm($term, true);
        return $this->ok($data);
    }

    /**
     * Default controller action. Forwards to the main edit page for a presentation
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function index(SS_HTTPRequest $r)
    {
        return $this->parent->redirect($this->Link('summary'));
    }

    /**
     * Controller action that handles the main page for creating or editing
     * a presentation
     * @param   $r SS_HTTPRequest
     * @return SSViewer
     */
    public function summary(SS_HTTPRequest $r)
    {
        return $this->customise(array(
            'Presentation' => $this->presentation
        ))->renderWith(array('PresentationPage_summary', 'PresentationPage'), $this->parent);
    }

    /**
     * Controller action that handles the list of speakers for the presentation
     * @param   $r SS_HTTPRequest
     * @return SSViewer
     */
    public function speakers(SS_HTTPRequest $r)
    {
        return $this->customise(array(
            'Presentation' => $this->presentation
        ))->renderWith(array('PresentationPage_speakers', 'PresentationPage'), $this->parent);
    }

    public function tags(SS_HTTPRequest $r)
    {
        return $this->customise(array(
            'Presentation' => $this->presentation
        ))->renderWith(array('PresentationPage_tags', 'PresentationPage'), $this->parent);
    }

    /**
     * Handles the deletion of a presentation
     * @param   $r SS_HTTPRequest
     * @return SSViewer
     */
    public function delete(SS_HTTPRequest $r)
    {
        try
        {
            if (!SecurityToken::inst()->check($r->getVar('t')))
                throw new Exception("Security token doesn't match.");
            $presentation_id = intval($r->param('PresentationID'));
            $this->getParent()->getPresentationManager()->removePresentation($presentation_id);
            return $this->parent->redirect($this->parent->Link());
        }
        catch(NotFoundEntityException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->httpError(404, 'Presentation does not exists');
        }
        catch(EntityValidationException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->httpError(412, $ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->httpError(403, 'You cannot delete this presentation');
        }
    }


    /**
     * Controller action that handles the "confirm" page
     * @param   $r SS_HTTPRequest
     * @return SSViewer
     */
    public function confirm(SS_HTTPRequest $r)
    {
        return $this->customise(array(
            'SuccessLink' => $this->Link('success'),
            'GoBackLink' => $this->Link('speakers'),
            'Presentation' => $this->presentation
        ))->renderWith(array('PresentationPage_confirm', 'PresentationPage'), $this->parent);
    }

    /**
     * Generates a preview iframe of the presentation
     * @param  SS_HTTPRequest $r
     * @return SSViewer
     */
    public function preview_iframe(SS_HTTPRequest $r)
    {
        return $this->customise(array(
            'Presentation' => $this->presentation
        ))->renderWith('PresentationPage_previewiframe');
    }

    /**
     * Controller action that handles the "success" page
     * @param   $r SS_HTTPRequest
     * @return SSViewer
     */
    public function success(SS_HTTPRequest $r)
    {
        try
        {
            $this->getParent()->getPresentationManager()->completePresentation
            (
                $this->presentation,
                new PresentationSpeakerNotificationEmailMessageSender,
                new PresentationCreatorNotificationEmailMessageSender,
                new PresentationModeratorNotificationEmailMessageSender
            );
            return $this->renderWith(array('PresentationPage_success', 'PresentationPage'), $this->parent);
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            Form::messageForForm('PresentationForm_PresentationForm', $ex1,'bad');
            return Controller::curr()->redirect($this->presentation->EditLink());
        }
        catch(Exception $ex){
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->httpError(404);
        }
    }

    public function createSaveActions($action, $step) {

        if($step > 1) {
            // add prev button
            $prev_action = new FormAction('PrevStep', 'Go Back');
            $prev_action->setButtonContent("<i class=\"fa fa-chevron-left\" aria-hidden=\"true\"></i>&nbsp;Go Back");
            $prev_action->addExtraClass('btn go-back-action-btn');
            $prev_action->setUseButtonTag(true);
            $array_actions[] = $prev_action;
        }

        $save_later_action = LinkFormAction::create('save_later')->setTitle('Save &amp; Come Back Later');
        $save_later_action->setCSSClass('save-later-action-btn');
        // default action btn
        $default_action    = FormAction::create($action, 'Save and continue');
        $default_action->addExtraClass("btn default-action-btn");
        $default_action->setUseButtonTag(true);
        $default_action->setButtonContent("Save and continue&nbsp;<i class=\"fa fa-chevron-right\" aria-hidden=\"true\"></i>");

        $array_actions[]   = $save_later_action;
        $array_actions[]   = $default_action;

        return $array_actions;
    }

    /**
     * Creates the presentation add/edit form
     * @return  PresentationForm
     */
    public function PresentationForm()
    {
        $actions = $this->createSaveActions('savePresentationSummary', 1);

        $form = PresentationForm::create
        (
            $this,
            "PresentationForm",
            new FieldList($actions),
            $this->Summit(),
            $this->parent->getPresentationManager(),
            $this->presentation
        );

        if ($data = Session::get("FormInfo.{$form->FormName()}.data")) {
            $form->loadDataFrom($data);
            return $form;
        }

        return ($this->presentation->exists())? $form->loadDataFrom($this->presentation):$form;
    }

    public function PresentationTagsForm()
    {
        $actions = $this->createSaveActions('savePresentationTags', 2);

        $form   = new PresentationTagsForm($this, 'PresentationTagsForm', new FieldList($actions), $this->presentation);

        if ($data = Session::get("FormInfo.{$form->FormName()}.data")) {
            return $form->loadDataFrom($data);
        }
        $form = $form->loadDataFrom($this->presentation);

        return $form;
    }

    /**
     * Creates the speaker add/edit form
     * @return  Form
     */
    public function AddSpeakerForm()
    {
        Requirements::customScript(sprintf("var speaker_search_url = '%s/%s'; ", $this->Link('speakers'), 'search'));

        $form = AddSpeakerForm::create(
            $this,
            "AddSpeakerForm",
            $this->presentation,
            $this->Summit()
        );

        return $form;
    }

    /**
     * Handles the form submission for saving the first step of the presentation
     * create or edit
     * @param   $data array
     * @param   $form Form
     * @return  SSViewer
     */
    public function savePresentationSummary($data, $form)
    {
        try
        {
            Session::set("FormInfo.{$form->FormName()}.data", $data);

            $rules = array
            (
                'Title'                   => 'required|max:100',
                'TypeID'                  => 'required',
                'Level'                   => 'required|text',
                'Abstract'                => 'required',
                'SocialSummary'           => 'required|max:100',
                'CategoryID'              => 'required|text'
            );

            $messages = array
            (
                'Title.required'                   => ':attribute is required.',
                'Title.max'                        => ':attribute must be less than 100 characters long.',
                'TypeID.required'                  => ':attribute is required.',
                'Level.required'                   => ':attribute is required.',
                'Abstract.required'                => ':attribute is required.',
                'SocialSummary.required'           => ':attribute is required.',
                'SocialSummary.max'                => ':attribute must be less than 100 characters long.',
                'CategoryID.required'              => 'Please choose a category group and then a category.'
            );

            $validator = ValidatorService::make($data, $rules, $messages);

            if($validator->fails()){
                throw new EntityValidationException($validator->messages());
            }

            $this->presentation = !$this->presentation->exists() ?
                $this->parent->getPresentationManager()->registerPresentationOn
                (
                    $this->getParent()->Summit(),
                    Member::currentUser(),
                    $data
                )
                :
                $this->parent->getPresentationManager()->updatePresentationSummary($this->presentation, $data);

            Session::clear("FormInfo.{$form->FormName()}.data");

            // next step
            $continue = $data['Continue'];
            if ($continue == 1) {
                return $this->parent->redirect($this->Link('tags'));
            } else {
                $vars = '?GoBackStep=manage/'.$this->presentation->ID.'/summary';
                return $this->parent->redirect($this->parent->Link().$vars);
            }

        }
        catch(EntityValidationException $ex1){
            $form->sessionMessage($ex1->getMessage(), 'bad');
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->redirectBack();
        }
        catch(NotFoundEntityException $ex2){
            $form->sessionMessage('There was an error with your request.', 'bad');
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->redirectBack();
        }
        catch(Exception $ex){
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            $form->sessionMessage('There was an error with your request.', 'bad');
            return $this->redirectBack();
        }
    }

    public function savePresentationTags($data, $form)
    {
        Session::set("FormInfo.{$form->FormName()}.data", $data);

        $this->presentation->setProgress(Presentation::PHASE_TAGS);
        $form->saveInto($this->presentation);
        $this->presentation->write();

        Session::clear("FormInfo.{$form->FormName()}.data");

        // next step
        $continue = $data['Continue'];
        if ($continue == 1) {
            return $this->parent->redirect($this->Link('speakers'));
        } else if ($continue == -1) {
            return $this->parent->redirect($this->Link('summary'));
        } else {
            $vars = '?GoBackStep=manage/'.$this->presentation->ID.'/tags';
            return $this->parent->redirect($this->parent->Link().$vars);
        }
    }

    /**
     * Handles the form submission that creates a new speaker.
     * Checks for existence, and uses existing if found
     * @param   $data array
     * @param   $form Form
     * @return  SSViewer
     */
    public function doAddSpeaker($data, $form)
    {

        try
        {
            $me    = Convert::raw2sql($data['SpeakerType']) == 'Me';
            $email = $me ? Member::currentUser()->Email : Convert::raw2sql($data['EmailAddress']);

            $rules = array
            (
                'SpeakerType'           => 'required',
                'EmailAddress'          => 'sometimes|required_if:SpeakerType,Else',
            );

            $messages = array
            (
                'SpeakerType.required'     => ':attribute is required.',
                'EmailAddress.sometimes'   => 'Please specify an email address.',
                'EmailAddress.required_if' => 'Please specify an email address.',
            );

            $member_id          = intval($data['MemberId']);
            $speaker_id         = intval($data['SpeakerId']);
            $provided_email     = !filter_var($email, FILTER_VALIDATE_EMAIL) === false;

            $validator          = ValidatorService::make($data, $rules, $messages);
            $member_repository  = $this->getParent()->getMemberRepository();
            $speaker_repository = $this->getParent()->getSpeakerRepository();


            if($validator->fails()){
                throw new EntityValidationException($validator->messages());
            }

            if($member_id === 0 && $speaker_id === 0 && !$provided_email)
                throw new EntityValidationException('Please specify an email address.');

            $member  = $me ? Member::currentUser() : $member_repository->getById($member_id);
            if(is_null($member) && $provided_email)
                $member = $member_repository->findByEmail($email);
            $speaker    = $speaker_repository->getById($speaker_id);

            $speaker = $this->getParent()->getPresentationManager()->addSpeakerByEmailTo
            (
                $this->presentation,
                $email,
                $member,
                $speaker
            );

            return $this->parent->redirect
            (
                Controller::join_links
                (
                    $this->Link(),
                    'speaker',
                    $speaker->getIdentifier()
                )
            );
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            $form->sessionMessage($ex1->getMessage(), 'bad');
            return $this->redirectBack();
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            $form->sessionMessage('There was an error with your request.', 'bad');
            return $this->redirectBack();
        }
    }

    /**
     * Handles the form submission for completing the "Add speakers" module
     *
     * @param $data
     * @param $form
     * @return SS_HTTPResponse
     * @throws ValidationException
     * @throws null
     */
    public function doFinishSpeaker($data, $form)
    {
        if ($this->presentation->getProgress() < Presentation::PHASE_SUMMARY) {
            return $this->parent->redirect($this->Link('summary'));
        } else {
            $this->presentation->setProgress(Presentation::PHASE_SPEAKERS)->write();
        }

        // next step
        $continue = $data['Continue'];
        if ($continue == 1) {
            return $this->parent->redirect($this->Link('confirm'));
        } else if ($continue == -1) {
            return $this->parent->redirect($this->Link('tags'));
        } else {
            $vars = '?GoBackStep=manage/'.$this->presentation->ID.'/speakers';
            return $this->parent->redirect($this->parent->Link().$vars);
        }
    }
}


/**
 * Handles all requests to update speakers on a presentation. Speaker
 * is loaded into the request object pre-screened for write privileges
 * and ensures the speaker is part of the parent Presentation request handler
 * @author  Aaron Carlino <aaron@unclecheeseproductions.com>
 */
class PresentationPage_ManageSpeakerRequest extends RequestHandler
{

    private static $allowed_actions = array(
        'EditSpeakerForm',
        'LegalForm',
        'ReviewForm',
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
     * @var  Presentation being updated
     */
    protected $presentation;

    /**
     * @var  PresentationPage_ManageRequest The parent controller
     */
    protected $parent;

    /**
     * Constructor for the request
     * @param   $speaker PresentationSpeaker The speaker being updated
     * @param   $parent PresentationPage_ManageRequest The parent controller
     */
    public function __construct(PresentationSpeaker $speaker, IPresentation $presentation, PresentationPage_ManageRequest $parent)
    {
        parent::__construct();
        $this->speaker = $speaker;
        $this->presentation = $presentation;
        $this->parent  = $parent;

        Requirements::css('summit/css/call-for-presentations.css');
    }

    /**
     * Helper for redirect back. Needed for validator.
     * @return SS_HTTPResponse
     */
    public function redirectBack()
    {
        return $this->parent->getParent()->redirectBack();
    }

    /**
     * Creates a link to this controller
     * @param   $action
     * @return  string
     */
    public function Link($action = null)
    {
        return Controller::join_links(
            $this->parent->Link(),
            'speaker',
            $this->speaker->ID ?: "new",
            $action
        );
    }

    /**
     * Default action for the controller. Forwards to an edit view
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function index(SS_HTTPRequest $r)
    {
        return $this->parent->getParent()->redirect($this->Link('edit'));
    }


    /**
     * Controller action that handles editing or creation of a speaker
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function edit(SS_HTTPRequest $r)
    {
        return $this->customise(array(
            'Speaker' => $this->speaker,
            'Presentation' => $this->presentation,
        ))->renderWith(array('PresentationPage_editspeaker', 'PresentationPage'), $this->parent->getParent());
    }


    /**
     * Controller action that handles deletion of a speaker
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function delete(SS_HTTPRequest $r)
    {
        if (!SecurityToken::inst()->check($r->getVar('t'))) {
            return $this->httpError(403, "Security token doesn't match.");
        }

        try {

            $this->parent->parent->getPresentationManager()->removeSpeakerFrom
            (
                $this->parent->getPresentation(),
                $this->speaker
            );
            return $this->parent->getParent()->redirect($this->parent->Link('speakers'));
        }
        catch(EntityValidationException $ex1){
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            $this->httpError(403, 'You cannot remove speakers from this presentation');
        }
        catch(Exception $ex){
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            $this->httpError(403, 'You cannot remove speakers from this presentation');
        }

    }


    /**
     * Controller action that handles the "update my details" page for a speaker
     * that has been assigned to a presentation
     * @param  SS_HTTPRequest $r
     * @return SSViewer
     */
    public function review(SS_HTTPRequest $r)
    {

        if (Member::currentUserID() != $this->speaker->MemberID) {
            return $this->httpError(403,
                sprintf('You are logged as Member %s, but this belongs to another speaker, please log out, and try it again.',
                    Member::currentUser()->Email));
        }

        return $this->customise(array(
            'Presentation' => $this->presentation
        ))->renderWith(array('PresentationPage_review', 'PresentationPage'), $this->parent->getParent());

    }


    /**
     * Controller action that handles the "speaker's bureau" module
     * @param   $r SS_HTTPRequest
     * @return  SSViewer
     */
    public function bureau(SS_HTTPRequest $r)
    {
        return $this->renderWith(array('PresentationPage_bureau', 'PresentationPage'), $this->parent->getParent());
    }


    /**
     * Controller action that sends the user to the legal agreement for video
     * @param  SS_HTTPRequest $r
     * @return SSViewer
     */
    public function legal(SS_HTTPRequest $r)
    {
        return $this->renderWith(array(
            'PresentationPage_legal',
            'PresentationPage'
        ), $this->parent->getParent());
    }


    /**
     * Creates an edit/create form for the speakers attached to a presentation
     * @return  SpeakerForm
     */
    public function EditSpeakerForm()
    {
        if (!$this->speaker->FirstName)
        {
            $this->speaker->FirstName = $this->speaker->Member()->FirstName;
        }

        if (!$this->speaker->LastName)
        {
            $this->speaker->LastName = $this->speaker->Member()->Surname;
        }

        $speaker_form = SpeakerForm::create
        (
            $this,
            "EditSpeakerForm",
            FieldList::create
            (
                FormAction::create('doSaveSpeaker', 'Save speaker details')
            ),
            $this->parent->Summit()
        )
        ->loadDataFrom($this->speaker);

        return $speaker_form;
    }

     /**
     * Creates the form that allows a speaker to consent to being on video
     * @return  Form
     */
    public function LegalForm()
    {
        return BootstrapForm::create(
            $this,
            "LegalForm",
            FieldList::create()
                ->literal('termbox', '<div class="termbox">' . $this->parent->getParent()->LegalAgreement . '</div>')
                ->checkbox('VideoAgreement', 'I agree to the terms above')
            , FieldList::create(
            FormAction::create('doSaveLegal', 'Continue')
        )
        );
    }


    /**
     * Creates the form that lets a speaker review his details. Merges
     * several forms together.
     * @return  Form
     */
    public function ReviewForm()
    {
        if (!$this->isMe()) {
            return $this->httpError(403);
        }

        $fields       = FieldList::create(HeaderField::create('Your details'));
        $dummy        = SpeakerForm::create($this, "EditSpeakerForm", FieldList::create());
        $fields->merge(
            $dummy->Fields()
        );
        $fields->bootstrapIgnore('Photo');

        $form = BootstrapForm::create(
            $this,
            "ReviewForm",
            $fields,
            FieldList::create(
                FormAction::create('doReviewForm', 'Save my details')
            ),
            $dummy->getSpeakerValidator()
        );
        if ($data = Session::get("FormInfo.{$form->FormName()}.data")) {
            return $form->loadDataFrom($data);
        }

        return $form->loadDataFrom($this->speaker);
    }


    /**
     * Processes the legal form
     * @param  array $data
     * @param  Form $form
     * @return SS_HTTPResponse
     */
    public function doSaveLegal($data, $form)
    {
        $form->saveInto($this->speaker);
        if (!$data['VideoAgreement']) {
            $form->sessionMessage('You must agree to the terms', 'bad');
            return $this->redirectBack();
        }

        $this->speaker->Member()->setSummitState('VIDEO_AGREEMENT_AGREED',
            $this->parent->Summit(),
            $this->parent->getParent()->LegalAgreement
        );

        return $this->parent->getParent()->redirect($this->parent->Link('speakers'));
    }


    /**
     * Handles the form submissions for creating/editing a speaker
     * @param   $data array
     * @param   $form Form
     * @return  SSViewer
     */
    public function doSaveSpeaker($data, $form)
    {
        $form->saveInto($this->speaker);

        $changed_fields = $this->speaker->getChangedFields(true,2);
        $this->speaker->write();

        if ((!empty($changed_fields) || $this->speaker->HasChanged == 1) && !$this->isMe()) {
            $current_user = Member::currentUser();
            $subject = "Attn: Your OpenStack Member Profile has been updated";
            $body = "A Presentation owner, ".$current_user->getName()." has just updated your Speaker Bio.
                Please double check https://www.openstack.org/profile/speaker to ensure everything looks as expected.
                If you find a problem with your bio, please send an email to speakersupport@openstack.org and we'll take a look right away.<br><br>
                Thank you,<br>
                OpenStack Speaker Support";

            $email = EmailFactory::getInstance()->buildEmail(null, $this->speaker->getEmail(), $subject, $body);
            $email->send();
        }

        $member = $this->speaker->Member();
        if (($member->ID > 0 && $member->getSummitState('BUREAU_SEEN', $this->parent->Summit())) || !$this->isMe()) {
            return $this->parent->getParent()->redirect($this->parent->Link('speakers'));
        }

        // Why an if that results in the same return ? ^^^^

        return $this->parent->getParent()->redirect($this->parent->Link('speakers'));
    }


    /**
     * @param $data
     * @param $form
     * @return bool|SS_HTTPResponse
     * @throws ValidationException
     * @throws null
     */
    public function doReviewForm($data, $form)
    {
        Session::set("FormInfo.{$form->FormName()}.data", $data);

        if (empty(strip_tags($data['Bio']))) {
            $form->addErrorMessage('Bio', 'Please enter a bio', 'bad');

            return $this->redirectBack();
        }
        $form->saveInto($this->speaker);
        $this->speaker->Member()->setSummitState('BUREAU_SEEN', $this->parent->Summit());
        if ($data['VideoAgreement'] == 1) {
            $this->speaker->Member()->setSummitState(
                'VIDEO_AGREEMENT_AGREED',
                $this->parent->Summit(),
                $this->parent->getParent()->LegalAgreement
            );
        } else {
            $this->speaker->Member()->setSummitState('VIDEO_AGREEMENT_DECLINED', $this->parent->Summit());
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
    protected function isMe()
    {
        return $this->speaker->MemberID == Member::currentUserID();
    }
}
