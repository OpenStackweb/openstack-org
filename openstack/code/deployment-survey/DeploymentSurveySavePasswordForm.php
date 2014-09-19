<?php

class DeploymentSurveySavePasswordForm extends Form {
 
   function __construct($controller, $name) {

      // Define fields //////////////////////////////////////

      $fields = new FieldList (
         new LiteralField('Username','<h4>Your username: <strong>'.Member::currentUser()->Email.'</strong></h4>'),
         new LiteralField('PasswordMessage','You can create a password to use when you return to your profile on OpenStack. Your password will be what you set below.'),
         new LiteralField('break','<hr/>'),
         new ConfirmedPasswordField('Password',
             'Password'
          )
      );

      $saveButton = new FormAction('SavePassword', 'Save Password');
	 
      $actions = new FieldList(
          $saveButton
      );

      // Create Validators
      $validator = new RequiredFields('Password');
   
      parent::__construct($controller, $name, $fields, $actions, $validator);

   }

   function SavePassword($data, $form) {
      if($data['Password'] != NULL) {
          $Member = Member::currentUser();
          if($Member) {
              $form->saveInto($Member);
              $Member->write();
          }
      }
      return Controller::curr()->redirect($form->controller->Link('ThankYou/?saved=1'));
   }
 
   function forTemplate() {
      return $this->renderWith(array(
         $this->class,
         'Form'
      ));
   }      
  
}