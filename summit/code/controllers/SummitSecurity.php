<?php

/**
 * A custom security controller to handle different layout and features
 * for the Summit section, as opposed to the CMS login.
 *
 * @author  Uncle Cheese <aaron@unclecheeseproductions.com>
 */
class SummitSecurity extends SummitPage_Controller {

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
     * @var IMemberManager
     */
    private $member_manager;

    /**
     * @return IMemberManager
     */
    public function getMemberManager()
    {
        return $this->member_manager;
    }

    /**
     * @param IMemberManager $manager
     */
    public function setMemberManager(IMemberManager $manager)
    {
        $this->member_manager = $manager;
    }

    /**
     * The URL segment of this controller
     * @var string
     */
    private static $url_segment = 'summit-login';

    /**
     * A list of allowed actions
     * @var array
     */
    private static $allowed_actions = array(
        'login',        
        'lostpassword',
        'passwordsent',
        'LostPasswordForm',
        'RegistrationForm',
        'LoginForm',
        'doRegister',
        'registration',
    );

    /**
     * Throw a 403 and redirect to this controller. Replaces Security::permissionFailure()  
     * @param  RequestHandler $c
     * @return SS_HTTPResponse
     */
    public static function permission_failure(RequestHandler $c) {
        return $c->redirect(HTTP::setGetVar(
            'BackURL', 
            $_SERVER['REQUEST_URI'],
            Controller::join_links(
                Director::baseURL(),
                'summit-login',
                'login'
            )
        ), 403);
    }


    public function CurrentCallForSpeakersPageUrl(){
        $current_summit = Summit::get_active();
        if(!$current_summit) return null;
        $presentation_page = PresentationPage::get()->filter('SummitID',$current_summit->ID)->first();
        if(!$presentation_page) return null;
        return $presentation_page->Link();
    }

    /**
     * @param null $action
     * @return String
     */
    public function Link($action = null) {
        return Controller::join_links($this->config()->url_segment, $action);
    }

    public function Summit(){
        return Summit::get_active();
    }

    public function ActiveSummit(){
        return Summit::ActiveSummit();
    }

    /**
     * Overload the redirect method so we can replace all references to Security
     * with its summit counterpart
     *     
     * @param  string  $url  
     * @param  integer $code [description]
     * @return SS_HTTPResponse
     */
    public function redirect($url, $code = 302) {
        return parent::redirect(preg_replace('/^Security\//', $this->config()->url_segment.'/', $url), $code);
    }

    /**
     * Ensure all root requests go to login
     * @return SS_HTTPResponse
     */
    public function index() {
        $back_url = $this->CurrentCallForSpeakersPageUrl();
        if(is_null($back_url))  return $this->httpError(404, "Call for Speakers Page not Found!");
        if(Member::currentUser())
            return $this->redirect($back_url);
        return $this->redirect($this->Link('login'));
    }

    /**
     * The login action, renders the login form
     * @return SS_HTTPResponse
     */
    public function login() {
        return $this->customiseSummitPage(
            array(
                'Title' => 'Login',
                'ClassName' => 'SummitSecurity',
                'Form' => $this->LoginForm()
            )
        )->renderWith(
            array(
                'SummitSecurity_login',
                'SummitPage'
            ),
            $this
        );
    }

    /**
     * Show the "lost password" page
     *
     * @return string Returns the "lost password" page as HTML code.
     */
    public function lostpassword() {
        $controller = $this;
        // if the controller calls Director::redirect(), this will break early
        if (($response = $controller->getResponse()) && $response->isFinished()) return $response;

        //Controller::$currentController = $controller;
        return $this->customiseSummitPage(
            array(
                'Title' => 'Lost Password',
                'ClassName' => 'SummitLostPassword'
            )
        )->renderWith(
            array(
                'SummitSecurity_lostpassword',
                'SummitPage'
            ),
            $this
        );
    }

    /**
     * Show the "password sent" page, after a user has requested
     * to reset their password.
     *
     * @param SS_HTTPRequest $request The SS_HTTPRequest for this action.
     * @return string Returns the "password sent" page as HTML code.
     */
    public function passwordsent($request) {
        $controller = $this;

        // if the controller calls Director::redirect(), this will break early
        if(($response = $controller->getResponse()) && $response->isFinished()) return $response;

        $email = Convert::raw2xml(rawurldecode($request->param('ID')) . '.' . $request->getExtension());

        return $this->customiseSummitPage(
            array(
                'Title' => 'Password Reset Link Sent',
                'Email' => $email,
                'ClassName' => 'SummitLostPassword'
            )
        )->renderWith(
            array(
                'SummitSecurity_passwordsent',
                'SummitPage'
            ),
            $this
        );
    }

    /**
     * Creates the registration form
     *
     * @return  BootstrapForm
     */

    public function RegistrationForm() {

        $speaker_registration_token = Session::get(SpeakerRegistrationRequest::ConfirmationTokenParamName);

        $fields =   FieldList::create(
            $first_name = TextField::create('FirstName','Your First Name'),
            $last_name  = TextField::create('Surname','Your Last Name'),
            $email      = EmailField::create('Email','Your email address'),
            $password   = ConfirmedPasswordField::create('Password','Password')
        );

        $password->setAttribute('required','true');

        //if we have in session a registration token, autopopulate values
        if(!empty($speaker_registration_token))
        {
            $request = $this->speaker_registration_request_repository->getByConfirmationToken($speaker_registration_token);

            if(is_null($request) || $request->alreadyConfirmed() )
                return $this->httpError(404, 'speaker registration request not found!');
            $first_name->setValue($request->proposedSpeakerFirstName());
            $last_name->setValue($request->proposedSpeakerLastName());
            $email->setValue($request->proposedSpeakerEmail());
        }

        $form = BootstrapForm::create(
            $this,
            'RegistrationForm',
            $fields,
            FieldList::create(
                FormAction::create('doRegister','Register now')
            ),
            RequiredFields::create('FirstName','Surname','Email')
        );

        $data = Session::get("FormInfo.{$form->getName()}.data");
        
        return $form->loadDataFrom($data ?: array ());
    }

    /**
     * Handles the registration. Validates and creates the member, then redirects
     * to the appropriate place
     *  
     * @param  array $data
     * @param  BootstrapForm $form
     * @return SSViewer
     */
    public function doRegister($data, $form) {
        try
        {
            $back_url                   = Session::get('BackURL');
            Session::set("FormInfo.{$form->getName()}.data", $data);
            $data                       = SQLDataCleaner::clean($data);
            $profile_page               = EditProfilePage::get()->first();
            $speaker_registration_token = Session::get(SpeakerRegistrationRequest::ConfirmationTokenParamName);

            if(!empty($speaker_registration_token))
            {
                $data[SpeakerRegistrationRequest::ConfirmationTokenParamName] = $speaker_registration_token;
            }

            $member = $this->member_manager->registerSpeaker($data, new MemberRegistrationSenderService);

            //Get profile page
            if (!is_null($profile_page)) {
                //Redirect to profile page with success message
                Session::clear("FormInfo.{$form->FormName()}.data");
                if ($back_url) {
                    $redirect = HTTP::setGetVar('welcome', 1, $back_url);
                    return OpenStackIdCommon::loginMember($member, $redirect);
                }
                $form->sessionMessage('Awesome! You should receive an email shortly.', 'good');
                Session::clear(SpeakerRegistrationRequest::ConfirmationTokenParamName);
                Session::clear('BackURL');
                return OpenStackIdCommon::loginMember($member, $this->redirectBackUrl());
            }

        }
        catch(EntityValidationException $ex1){
            Form::messageForForm($form->FormName(), $ex1->getMessage(), 'bad');
            //Return back to form
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->redirectBack();
        }
        catch(Exception $ex){
            Form::messageForForm($form->FormName(), "There was an error with your request, please contact your admin.", 'bad');
            //Return back to form
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->redirectBack();
        }
    }

    /**
     * Factory method for the lost password form
     *
     * @return Form Returns the lost password form
     */
    public function LostPasswordForm() {        
        return BootstrapMemberLoginForm::create(         $this,
            'LostPasswordForm',
            new FieldList(
                new EmailField('Email', _t('Member.EMAIL', 'Email'))
            ),
            new FieldList(
                new FormAction(
                    'forgotPassword',
                    _t('Security.BUTTONSEND', 'Send me the password reset link')
                )
            ),
            false
        );
    }

    /**
     * Creates a login form. Replaces the hardcoded Security/ link with the link
     * to this controller
     *
     * @return  MemberLoginForm
     */
    public function LoginForm() {

        $back_url = $this->CurrentCallForSpeakersPageUrl();
        if(is_null($back_url))  return $this->httpError(404, "Summit Speakers Not Found!");

        if($this->request->getVar('BackURL')){
            $back_url = $this->request->getVar('BackURL');
        }

        $form =  OpenStackIdFormsFactory::buildLoginForm($this, $back_url);

        $form->setActions(FieldList::create(
            new FormAction('dologin', _t('Member.BUTTONLOGIN', "Log in")),
            new LiteralField(
                'forgotPassword',
                '<p id="ForgotPassword"><a href="summit-login/lostpassword">'
                . _t('Member.BUTTONLOSTPASSWORD', "I've lost my password") . '</a></p>'
            )
        ));
        return $form;
    }

    /**
     * Helper method for customising. Injects all the context of the summit page
     * by getting the home page of the Summit section
     * 
     * @param  array  $data 
     * @return string
     */
    protected function customiseSummitPage($data = array ()) {
        return ModelAsController::controller_for(
            SummitOverviewPage::get()->first()
        )->customise($data);
    }


    public function redirectBackUrl() {
        // Don't cache the redirect back ever
        HTTP::set_cache_age(0);

        $url = null;

        // In edge-cases, this will be called outside of a handleRequest() context; in that case,
        // redirect to the homepage - don't break into the global state at this stage because we'll
        // be calling from a test context or something else where the global state is inappropraite
        if($this->request) {
            if($this->request->requestVar('BackURL')) {
                $url = $this->request->requestVar('BackURL');
            } else if($this->request->isAjax() && $this->request->getHeader('X-Backurl')) {
                $url = $this->request->getHeader('X-Backurl');
            } else if($this->request->getHeader('Referer')) {
                $url = $this->request->getHeader('Referer');
            }
        }

        if(!$url) $url = Director::baseURL();

        // absolute redirection URLs not located on this site may cause phishing
        if(Director::is_site_url($url)) {
           return $url;
        } else {
            return false;
        }

    }

    public function registration(){

        $speaker_registration_token = Session::get(SpeakerRegistrationRequest::ConfirmationTokenParamName);

        if(!empty($speaker_registration_token)){

            $request = $this->speaker_registration_request_repository->getByConfirmationToken($speaker_registration_token);

            if(is_null($request) || $request->alreadyConfirmed())
                return $this->httpError(404, 'speaker registration request not found!');
        }

        return $this->customiseSummitPage(array())->renderWith(
            array(
                'SummitSecurity_registration',
                'SummitPage'
            ),
            $this
        );
    }

    public function CurrentSummitPage(){
        $summit = Summit::get_active();
        $page = SummitOverviewPage::get()->filter('SummitID', $summit->ID)->first();
        if(is_null($page)) $page = SummitStaticAboutPage::get()->filter('SummitID', $summit->ID)->first();
        return $page;
    }

    /**
     * Returns the associated database record
     */
    public function data() {
        return $this->CurrentSummitPage();
    }

    public function SummitRoot(){
        $summit_page = $this->CurrentSummitPage();
        return !is_null($summit_page)? $summit_page->Link(): '#';
    }

    public function PresentationDeadlineText(){
        $summit = Summit::get_active();
        $page   = PresentationPage::get()->filter('SummitID', $summit->ID)->first();
        return $page->PresentationDeadlineText;
    }
}