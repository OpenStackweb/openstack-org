<?php

/**
 * Adds a set of methods to controllers to check authentication, including
 * a simple form for setting user password after authenticating via a token
 *
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 */
class MemberTokenAuthenticator extends DataExtension {


	private static $allowed_actions = array (
		'setpassword',
		'SetPasswordForm',
		'doSetPassword'
	);


	/**
	 * Checks the token in the request and produdes three possible results:
	 *
	 * - Return true if token was found and member was logged in
	 * - Return false if there was no token in the request
	 * - Throw an exception if a token was in the request, but invalid
	 * 		
	 * @param  boolean $required If true, throw an exception if there is no token
	 * @return boolean
	 * @throws   If token is present, but invalid
	 */
	public function checkAuthenticationToken($required = false) {
		if($token = $this->owner->getRequest()->requestVar('token')) {
			if($member = Member::get()->filter('AuthenticationToken', $token)->first()) {
				if($member->checkToken()) {
					$member->setTokenExpiry();
					$member->write();
					$member->login();

					return true;
				}

				return $this->owner->httpError(403, "Token is expired");
			}

			return $this->owner->httpError(403, "Invalid token");
		}
		if($required) {
			return $this->owner->httpError(403, "You must provide an authentication token");
		}

		return false;
	}


	/**
	 * Creates a form to set a password
	 *
	 * @return  Form
	 */
	public function SetPasswordForm() {
		if(!Member::currentUser()) return false;

		$form = Form::create(
			$this->owner,
			FieldList::create(
				PasswordField::create('Password', 'Password'),
				PasswordField::Create('Password_confirm','Confirm password'),
				HiddenField::create('BackURL','', $this->owner->requestVar('BackURL'))
			),
			FieldList::create(
				FormAction::create('doSetPassword','Set my password')
			),
			RequiredFields::create('Password','Password_confirm')
		);

		return $form;
	}


	/**
	 * Handles the SetPassword form
	 * @param  array $data 
	 * @param  Form $form 	 
	 */
	public function doSetPassword($data, $form) {
		if(!Member::currentUser()) return false;

		if($data['Password'] && $data['Password'] == $data['Password_confirm']) {
			Member::currentUser()->Password = $data['Password'];
			Member::currentUser()->write();

			if($data['BackURL']) {
				return $this->owner->redirect($data['BackURL']);
			}

			$form->sessionMessage('Password updated','good');
		}

		$form->sessionMessage('Passwords do not match','bad');

		return $this->owner->redirectBack();
	}
}