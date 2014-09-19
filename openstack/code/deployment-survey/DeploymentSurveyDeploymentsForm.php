<?php

class DeploymentSurveyDeploymentsForm extends Form {
 
   function __construct($controller, $name) {

      // Define fields //////////////////////////////////////
   
      $fields = new FieldList (
      );

      $prevButton = new FormAction('PreviousStep', 'Previous Step');
      $nextButton = new FormAction('NextStep', 'Next Step');
   
      $actions = new FieldList(
          $prevButton, $nextButton
      );

   
      parent::__construct($controller, $name, $fields, $actions);

   }
 
   function forTemplate() {
      return $this->renderWith(array(
         $this->class,
         'Form'
      ));
   }      
  
}