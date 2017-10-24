<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
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

		Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

		JSChosenDependencies::renderRequirements();
		JQueryValidateDependencies::renderRequirements();

   		$css_files = array(
			"registration/css/affiliations.css",
			'registration/css/registration.page.css'
		);

		foreach($css_files as $css_file)
			Requirements::css($css_file);

        Requirements::combine_files('speaker_voting_registration_page.js', array(
            "node_modules/pure/libs/pure.min.js",
            "themes/openstack/javascript/jquery.serialize.js",
            "themes/openstack/javascript/jquery.cleanform.js",
            "registration/javascript/affiliations.js",
            "registration/javascript/registration.page.js",
        ));

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


		//Get current voting page and redirect there
		if ($VotingPage = PresentationVotingPage::get()->first()) {
			$BackURL = Session::get('BackURL');
			if ($BackURL) {
                $BackURL = $VotingPage->Link() . 'Presentation/' . $BackURL;
			} else {
                $BackURL = $VotingPage->Link();
			}
		}

        return OpenStackIdCommon::loginMember($Member, $BackURL);
	}
}