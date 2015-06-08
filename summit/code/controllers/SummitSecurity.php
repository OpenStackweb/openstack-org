<?php

/**
 * A custom security controller to handle different layout and features
 * for the Summit section, as opposed to the CMS login.
 *
 * @author  Uncle Cheese <aaron@unclecheeseproductions.com>
 */
class SummitSecurity extends Controller {

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
        'doRegister'        
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

    /**
     * Generate a link to this controller
     * @param string $action
     */
    public function Link($action = null) {
        return Controller::join_links($this->config()->url_segment, $action);
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
     * @return  BoostrapForm
     */
    public function RegistrationForm() {
        $url = Controller::curr()->getRequest()->requestVar('BackURL');

        $form = BootstrapForm::create(
            $this,
            'RegistrationForm',
            FieldList::create(
                EmailField::create('Email','Your email address'),
                PasswordField::create('Password','Password'),
                PasswordField::create('Password_confirm','Confirm your password'),
                HiddenField::create('BackURL','', $url)
            ),
            FieldList::create(
                FormAction::create('doRegister','Register now')
            ),
            RequiredFields::create('Email','Password','Password_confirm')
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
        Session::set("FormInfo.{$form->getName()}.data", $data);
        $member = Member::get()->filter('Email', $data['Email'])->first();
        if($member) {
            $form->sessionMessage('Bah! We\'ve already got a user with that email.','bad');
            return $this->redirectBack();
        }

        if($data['Password'] != $data['Password_confirm']) {
            $form->sessionMessage('Passwords do not match.','bad');
            return $this->redirectBack();
        }

        $member = Member::create(array(
            'Email' => $data['Email'],
            'Password' => $data['Password']
        ));
        $member->write();
        $member->addToGroupByCode('speakers');
        $member->sendWelcomeEmail();
        $member->login();

        Session::clear("FormInfo.{$form->getName()}.data");

        if($data['BackURL']) {
            $redirect = HTTP::setGetVar('welcome', 1, $data['BackURL']);
            return $this->redirect($redirect);
        }

        $form->sessionMessage('Awesome! You should receive an email shortly.','good');
        return $this->redirectBack();
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

        $back_url = $this->request->getVar('BackURL');
        if(empty($back_url))
            $back_url = $this->join_links(Director::baseURL(), $this->Link());

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

}