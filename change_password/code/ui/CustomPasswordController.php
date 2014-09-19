<?php
/***
 * Class CustomPasswordController
 */
class CustomPasswordController extends Security {

	private static $allowed_actions = array(
		'changepassword',
		'ChangePasswordForm',
	);

	/**
	 * @var PasswordManager
	 */
	private $password_manager;

	public function __construct(){
		parent::__construct();
		$this->password_manager = new PasswordManager;
	}

	/**
	 * Factory method for the lost password form
	 *
	 * @return Form Returns the lost password form
	 */
	public function ChangePasswordForm() {
		return new CustomChangePasswordForm($this, 'ChangePasswordForm');
	}

	/**
	 * @return string
	 */
	public function changepassword() {
		$tmpPage             = new Page();
		$tmpPage->Title      = _t('Security.CHANGEPASSWORDHEADER', 'Change your password');
		$tmpPage->URLSegment = 'Security';
		$tmpPage->ID         = -1; // Set the page ID to -1 so we dont get the top level pages as its children
		$controller          = new Page_Controller($tmpPage);
		$controller->init();

		try{
			$former_hash = Session::get('AutoLoginHash');
			if(!empty($former_hash)){
				// Subsequent request after the "first load with hash"
				$customisedController = $controller->customise(array(
					'Content' =>
						'<p>' .
						_t('Security.ENTERNEWPASSWORD', 'Please enter a new password.') .
						'</p>',
					'Form' => $this->ChangePasswordForm(),
				));
			}
			else{
				$new_hash  = $this->password_manager->verifyToken((int)@$_REQUEST['m'], @$_REQUEST['t']);
				Session::set('AutoLoginHash', $new_hash);
				return $this->redirect($this->Link('changepassword'));
			}
		}
		catch(InvalidPasswordResetLinkException $ex1){
			$customisedController = $controller->customise(
				array('Content' =>
					sprintf('<p>This link is no longer valid as a newer request for a password reset has been made. Please check your mailbox for the most recent link</p><p>You can request a new one <a href="%s">here',
						$this->Link('lostpassword'))
				)
			);
		}
		return $customisedController->renderWith(array('Security_changepassword', 'Security', $this->stat('template_main'), 'ContentController'));
	}
}