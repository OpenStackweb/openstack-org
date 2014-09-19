<?php
class SpeakerVotingLoginForm extends MemberLoginForm {
    public function dologin($data) {
        if($this->performLogin($data)) {
	        Controller::curr()->redirectBack();
        } else {
            if($badLoginURL = Session::get("BadLoginURL")) {
	            Controller::curr()->redirect($badLoginURL);
            } else {
	            Controller::curr()->redirectBack();
            }
        }      
    }

    function __construct($controller, $name, $fields = null, $actions = null,
                                             $checkCurrentUser = true) {

            if(!$actions) {
                $actions = new FieldList(
                    new FormAction('dologin', _t('Member.BUTTONLOGIN', "Log in")),
                    new LiteralField(
                        'forgotPassword',
                        '<p id="ForgotPassword"><a href="/Security/lostpassword">' . _t('Member.BUTTONLOSTPASSWORD', "I've lost my password") . '</a></p>'
                    )
                );
            }

            parent::__construct($controller, $name, $fields, $actions);

    }    
}