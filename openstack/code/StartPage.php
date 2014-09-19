<?php
/**
 * Defines the JobsHolder page type
 */
class StartPage extends Page {
   static $db = array(
      'Summary' => 'HTMLText'
   );
   static $has_one = array(
   );
 
   static $allowed_children = array(NULL);
   /** static $icon = "icon/path"; */

   function getCMSFields() {
      $fields = parent::getCMSFields();
      
      // the date field is added in a bit more complex manner so it can have the dropdown date picker
      $SummaryField = new TextareaField('Summary','Quick summary:');
      $fields->addFieldToTab('Root.Main', $SummaryField, 'Content');
          
      return $fields;
   }      
      
}
 
class StartPage_Controller extends Page_Controller {
	
	function init() {
	    parent::init();
	}

  function StartOverview() {
    $StartPageHolder = StartPageHolder::get()->first();
    return $StartPageHolder->Content;
  }   
	
}