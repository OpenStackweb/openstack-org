<?php

class PresentationVotingSearchForm extends Form {
 
   function __construct($controller, $name) {
   

		$SearchField = new TextField('Search', 'Search');
   
		$fields = new FieldList(
		     $SearchField
		);

      $searchButton = new FormAction('doSearch', 'Search');
      $searchButton->addExtraClass('button');
	 
       $actions = new FieldList(
          $searchButton
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