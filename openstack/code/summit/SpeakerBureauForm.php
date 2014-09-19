<?php

class SpeakerBureauForm extends HoneyPotForm {
 
   function __construct($controller, $name) {


      // Get the city for the current member
      if($Member = Member::currentUser()) {
         $country = $Member->Country;
      } else {
         $country = '';
      }

      // Opt In Field
      $OptInField = new CheckboxField ('AviableForBureau',"I'd like to be in the speaker bureau.");

      $Divider = new LiteralField ('hr','<hr/>');

      // Funded Travel
      $FundedTravelField = new CheckboxField ('FundedTravel',"My company would be willing to fund my travel to events.");      
                           
      // Country Field
      $CountryCodes = CountryCodes::$iso_3166_countryCodes;
      $CountryField = new DropdownField('Country', 'Country', $CountryCodes);
      $CountryField->setValue($country);

      $ExpertiseField = new TextareaField('Expertise', 'My Areas of Expertise (one per line)');

      $fields = new FieldList(
         $OptInField,
         $Divider,
         $FundedTravelField,
         $CountryField,
         $ExpertiseField
      );      
      
      $actions = new FieldList(
         new FormAction('saveAction', 'Save Preferences'),
         new FormAction('skipAction', 'Skip This Step')
      );

      parent::__construct($controller, $name, $fields, $actions);
   }
 
   function forTemplate() {
      return $this->renderWith(array(
         $this->class,
         'Form'
      ));
   }
   
   function saveAction($data, $form) {

      if($Member = Member::currentUser()) {
         $speaker = Speaker::get()->filter('MemberID',$Member->ID)->first();
         if ($speaker) {
            $form->saveInto($speaker);
            $speaker->AskedAboutBureau = TRUE;
            $speaker->write();
         }
         if(($data['Country'] != '') && ($data['Country'] != $Member->Country)) {
            $Member->Country = convert::raw2sql($data['Country']);
            $Member->write();
         }
      }

      $TalkID = Session::get('SpeakerBureau.TalkID');
      Session::clear('SpeakerBureau.TalkID');

      if($TalkID) {
	      Controller::curr()->redirect($form->controller()->Link().'SpeakerList/'.$TalkID);
      } else {
	      Controller::curr()->redirect($form->controller()->Link());
      }
   }

   function skipAction($data, $form) {
	   Controller::curr()->redirect($form->controller()->Link().'?bureau=0');
   }
}