<?php

class OnsitePhoneForm extends Form {
 
   function __construct($controller, $name, $speakerHash) {
   

		$PhoneField = new TextField('PhoneNumber', 'Your Onsite Phone Number in Hong Kong');

    // Speaker Hash Field
    $SpeakerHashField = new HiddenField('speakerHash', "speakerHash", $speakerHash); 
   
		$fields = new FieldList(
		     $PhoneField,
         $SpeakerHashField
		);

      $submitButton = new FormAction('doSavePhoneNumber', 'Save');
	 
       $actions = new FieldList(
          $submitButton
       );
   

      $validator = new RequiredFields('PhoneNumber');
      parent::__construct($controller, $name, $fields, $actions, $validator);

   }
 
   function forTemplate() {
      return $this->renderWith(array(
         $this->class,
         'Form'
      ));
   } 

   function doSavePhoneNumber($data, $form) {

      if(isset($data['speakerHash'])) $hashKey = Convert::raw2sql($data['speakerHash']);
      if(isset($hashKey)) $speakerID = substr(base64_decode($hashKey),3);

      if(isset($speakerID) &&  is_numeric($speakerID) && isset($data['PhoneNumber']) && $data['PhoneNumber'] != '' && $Speaker = Speaker::get()->byID($speakerID))
      {
        $Speaker->OnsiteNumber = Convert::raw2sql($data['PhoneNumber']);
        $Speaker->write();
        Controller::curr()->redirect(Controller::curr()->Link().'PhoneNumberSaved/');
      }


   }
  
}