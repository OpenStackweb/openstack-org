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
class OnsitePhoneForm extends Form {
 
   function __construct($controller, $name, $speakerHash) {
       
    $CompanyField = new TextField('Company','Name of Your Company');
    $PhoneField = new TextField('PhoneNumber', 'Your Onsite Phone Number in Vancouver');
    $VideoAgreementField = new OptionsetField(
        'AgreedToVideo',
        'Do you agree to the terms above?',
        array(
            "0" => "No, I do not wish to be on video.",
            "1" => "Yes, I agree. It's okay to record my session." 
        ),
        1
    );       
       
    // Speaker Hash Field
    $SpeakerHashField = new HiddenField('speakerHash', "speakerHash", $speakerHash); 
   
        $fields = new FieldList(
            $VideoAgreementField,
            new LiteralField('hr','<hr/>'),
            new LiteralField('step','<strong>Last thing:</strong> To help ensure great communication and coordination, please provide your company and a phone number that we can reach you at while onsite at the Vancouver Summit.'),
            $CompanyField,
            $PhoneField,
            $SpeakerHashField
        );

      $submitButton = new FormAction('doSavePhoneNumber', 'Confirm My Speaking Invitation');
	 
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
        $Speaker->OnsiteNumber = $data['PhoneNumber'];
        $Speaker->Company = $data['Company'];
        $Speaker->AgreedToVideo = $data['AgreedToVideo'];          
        
        $Speaker->write();
        Controller::curr()->redirect(Controller::curr()->Link().'PhoneNumberSaved/');
      }


   }
  
}