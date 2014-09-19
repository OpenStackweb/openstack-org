<?php

class DeploymentSurveyRegistrationForm extends Form {

   function __construct($controller, $name) {

      // Define fields //////////////////////////////////////

      $fields = new FieldList (
        new TextField('FirstName','First Name'),
        new TextField('Surname','Last Name'),
        new TextField('Email','Email')
      );

      $startSurveyButton = new FormAction('StartSurvey', 'Start Survey');
      $actions = new FieldList(
          $startSurveyButton
      );



      $validator = new RequiredFields("FirstName","Surname","Email");


      parent::__construct($controller, $name, $fields, $actions, $validator);

   }

   function forTemplate() {
      return $this->renderWith(array(
         $this->class,
         'Form'
      ));
   }


   function StartSurvey($data, $form) {


		//Check for existing member email address
		if($member = Member::get()->filter('Email',Convert::raw2sql($data['Email']))->first())
		{
			//Set error message
			$form->AddErrorMessage('Email', "Sorry, that email address already exists. Please choose another or login with that email.", 'bad');
			//Set form data from submitted values
			Session::set("FormInfo.Form_DeploymentSurveyRegistrationForm.data", $data);
			//Return back to form
			return Controller::curr()->redirectBack();
		}

		//Otherwise create new member and log them in
		$Member = new Member();
		$form->saveInto($Member);
		$Member->write();

		$Member->login();

		//Find or create the 'user' group

		if(!$userGroup = Group::get()->filter('Code','users')->first())
		{
			$userGroup = new Group();
			$userGroup->Code = "users";
			$userGroup->Title = "Users";
			$userGroup->Write();
			$Member->Groups()->add($userGroup);
		}
		//Add member to user group
		$Member->Groups()->add($userGroup);

        return Controller::curr()->redirect($form->controller->Link()."OrgInfo");
   }
}