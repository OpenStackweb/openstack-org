<?php

class SigninForm extends Form {
 
   function __construct($controller, $name) {
   
   
	   	// Name Set
		$FirstNameField = new TextField('FirstName', 'First Name'); 
		$FirstNameField->addExtraClass('input-text');
		$LastNameField = new TextField('LastName', 'Last Name'); 
		$LastNameField->addExtraClass('input-text');
				
		// Email
		$EmailAddressField = new EmailField('EmailAddress', 'Email Address');
		$EmailAddressField->addExtraClass('input-text');	
   
		$fields = new FieldList(
		     $FirstNameField,
		     $LastNameField,
		     $EmailAddressField
		);
	 
       $actions = new FieldList(
          new FormAction('doSigninForm', 'Sign Up')
       );
   
      parent::__construct($controller, $name, $fields, $actions);
      
      // Create Validators
      $validator = new RequiredFields('FirstName');
      $validator = new RequiredFields('LastName');
      $validator = new RequiredFields('EmailAddress');
      
      $this->disableSecurityToken();
      
      
   }
 
   function forTemplate() {
      return $this->renderWith(array(
         $this->class,
         'Form'
      ));
   }
 
   function submit($data, $form) {
      // do stuff here
   }
 
}