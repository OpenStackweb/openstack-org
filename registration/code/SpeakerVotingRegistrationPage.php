<?php

class SpeakerVotingRegistrationPage extends Page
{

}

class SpeakerVotingRegistrationPage_Controller extends Page_Controller
{

	//Allow our form as an action
	static $allowed_actions = array(
		'Form'
	);

	function init()
	{
		parent::init();

		Requirements::css("themes/openstack/css/chosen.css", "screen,projection");
		Requirements::css("themes/openstack/javascript/jquery-ui-1.10.3.custom/css/smoothness/jquery-ui-1.10.3.custom.min.css");
		Requirements::css("registration/css/affiliations.css");


		Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");
		Requirements::javascript("themes/openstack/javascript/pure.min.js");
		Requirements::javascript("themes/openstack/javascript/jquery.serialize.js");
		Requirements::javascript("themes/openstack/javascript/jquery.cleanform.js");
		Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
		Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
		Requirements::javascript("themes/openstack/javascript/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js");
		Requirements::javascript("themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js");
		Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");

		Requirements::css('registration/css/registration.page.css');
		Requirements::javascript("registration/javascript/affiliations.js");
		Requirements::javascript("registration/javascript/registration.page.js");

		$getVars = $this->request->getVars();

		if (isset($getVars['BackURL'])) {
			$URL = Convert::raw2sql($getVars['BackURL']);
			Session::set('BackURL', $URL);
		}

	}


	//Generate the registration form
	function Form()
	{
		// Name Set
		$FirstNameField = new TextField('FirstName', "First Name");
		$LastNameField = new TextField('Surname', "Last Name");

		// Email Addresses
		$PrimaryEmailField = new TextField('Email', "Primary Email Address");
		$fields = new FieldList(
			$FirstNameField,
			$LastNameField,
			new LiteralField('break', '<hr/>'),
			$PrimaryEmailField,
			new LiteralField('instructions', '<p>This will also be your login name.</p>'),
			new LiteralField('break', '<hr/>'),
			new ConfirmedPasswordField('Password', 'Password')
		);

		$actions = new FieldList(
			new FormAction('doRegister', 'Start Voting')
		);


		$validator = new Member_Validator(
			'FirstName',
			'Surname',
			'Email',
			'Password'
		);

		return new SafeXSSForm($this, 'Form', $fields, $actions, $validator);
	}

	//Submit the registration form
	function doRegister($data, $form)
	{

		//Check for existing member email address
		if ($member = Member::get()->filter('Email', Convert::raw2sql($data['Email']))->first()) {
			//Set error message
			$form->AddErrorMessage('Email', "Sorry, that email address already exists. Please choose another.", 'bad');
			//Set form data from submitted values
			Session::set("FormInfo.SafeXSSForm_Form.data", $data);
			//Return back to form
			return $this->redirectBack();;
		}

		//Otherwise create new member and log them in
		$Member = new Member();
		$form->saveInto($Member);
		$Member->write();

		//Find or create the 'user' group
		if (!$userGroup = Group::get()->filter('Code', 'presentation-voters')->first()) {
			$userGroup = new Group();
			$userGroup->Code = "presentation-voters";
			$userGroup->Title = "Presentation Voters";
			$userGroup->Write();
			$Member->Groups()->add($userGroup);
		}
		//Add member to user group
		$Member->Groups()->add($userGroup);

		$Member->login();

		//Get current voting page and redirect there
		if ($VotingPage = PresentationVotingPage::get()->first()) {
			$BackURL = Session::get('BackURL');
			if ($BackURL) {
				return $this->redirect($VotingPage->Link() . 'Presentation/' . $BackURL);
			} else {
				return $this->redirect($VotingPage);
			}
		}

	}
}