<?php

/**
 * A custom security controller to handle different layout and features
 * for the Summit section, as opposed to the CMS login.
 *
 * @author  Uncle Cheese <aaron@unclecheeseproductions.com>
 */
class SummitSecurity extends SummitPage_Controller {

    /**
     * The URL segment of this controller
     * @var string
     */
    private static $url_segment = 'summit-login';

    /**
     * A list of allowed actions
     * @var array
     */
    private static $allowed_actions = [
        'login',
        'LoginForm',
        'doRegister',
        'registration',
    ];

    /**
     * @var ISpeakerRegistrationRequestRepository
     */
    private $speaker_registration_request_repository;

    /**
     * @var IMemberManager
     */
    private $member_manager;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var ISpeakerRegistrationRequestManager
     */
    private $speaker_registration_request_manager;


    private $summit_page;


    public function init()
    {
        parent::init();
        $this->summit_page = $this->currentSummitPage();
    }

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
     * @param ITransactionManager $tx_manager
     */
    public function setTransactionManager(ITransactionManager $tx_manager){
        $this->tx_manager = $tx_manager;
    }

    /**
     * @return ITransactionManager
     */
    public function getTransactionManager(){
        return $this->tx_manager;
    }

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
       return CFP_APP_BASE_URL;
    }

    /**
     * @param null $action
     * @return String
     */
    public function Link($action = null) {
        return Controller::join_links($this->config()->url_segment, $action);
    }

    public function Summit(){
        return Summit::ActiveSummit();
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
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse|void
     */
    public function index(SS_HTTPRequest $request) {
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
     * Creates a login form. Replaces the hardcoded Security/ link with the link
     * to this controller
     *
     * @return  MemberLoginForm
     */
    public function LoginForm() {

        $back_url = $this->CurrentCallForSpeakersPageUrl();
        if(is_null($back_url)) return $this->httpError(404, "Summit Speakers Not Found!");

        $form =  OpenStackIdFormsFactory::buildLoginForm($this, $back_url);

        $form->setActions(FieldList::create(
            new FormAction('dologin', _t('Member.BUTTONLOGIN', "Log in"))
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
            $this->summit_page
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

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse|void
     */
    public function registration(SS_HTTPRequest $request){

        $speaker_registration_token = $request->getVar(SpeakerRegistrationRequest::ConfirmationTokenParamName);
        $backUrl                    = $request->getVar('BackURL');
        if(!empty($speaker_registration_token) && !empty($backUrl)){
            if(!OpenStackIdCommon::isAllowedBackUrl($backUrl)){
                return $this->httpError(404, 'speaker registration request not found!');
            }
            Session::set(SpeakerRegistrationRequest::ConfirmationTokenParamName, $speaker_registration_token);
            Session::set('BackURL', $backUrl);
            return $this->redirect($this->Link('registration'));
        }


        $speaker_registration_token = Session::get(SpeakerRegistrationRequest::ConfirmationTokenParamName);
        $backUrl                    = Session::get('BackURL');

        if(empty($speaker_registration_token) && empty($backUrl)){
            return $this->httpError(404, 'speaker registration request not found!');
        }

         $speaker_registration_request = $this->speaker_registration_request_repository->getByConfirmationToken($speaker_registration_token);

         if(is_null($speaker_registration_request) || $speaker_registration_request->alreadyConfirmed()) {
             Session::clear(SpeakerRegistrationRequest::ConfirmationTokenParamName);
             return $this->httpError(404, 'speaker registration request not found!');
         }

        $registration_url = OpenStackIdCommon::getRegistrationUrl(Director::absoluteURL('/Security/login'));
        $this->redirect($registration_url.sprintf("&first_name=%s&last_name=%s&email=%s",
                $speaker_registration_request->Speaker()->FirstName,
                $speaker_registration_request->Speaker()->LastName,
                $speaker_registration_request->Email));
    }

    public function CurrentSummitPage(){
        $activeSummit = Summit::ActiveSummit();
        $summitPage = SummitPage::get()->filter('SummitID', $activeSummit->ID)->first();
        if (is_a($summitPage->Parent(),'SummitPage')) {
            $summitPage = $summitPage->Parent();
        }

        if(is_null($summitPage))
            $summitPage = SummitPage::get()->sort('CreatedDate', 'DESC')->first();

        return $summitPage;
    }

    /**
     * Returns the associated database record
     */
    public function data() {
        return $this->summit_page;
    }

    public function SummitRoot(){
        return !is_null($this->summit_page)? $this->summit_page->Link(): '#';
    }

    public function PresentationDeadlineText(){
        $summit = Summit::ActiveSummit();
        $page   = PresentationPage::get()->filter('SummitID', $summit->ID)->first();
        return $page->PresentationDeadlineText;
    }

    public function MetaTags()
    {
        return ModelAsController::controller_for($this->summit_page)->MetaTags();
    }

    public function getSummitPageText($field) {
        return ModelAsController::controller_for($this->summit_page)->getSummitPageText($field);
    }

}
