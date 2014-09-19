<?php
/**
 * Defines the ConferenceNewsPage page type
 */
class ConferenceNewsPage extends Page {
   static $db = array(
	);
   static $has_one = array(
   );
   static $defaults = array ( 
     'ShowInMenus' => false, 
     'ShowInSearch' => false 
    );
   
 	function getCMSFields() {
    	$fields = parent::getCMSFields();
    	
    	return $fields;
 	}   
}
 
class ConferenceNewsPage_Controller extends Page_Controller {
	function init() {
	    parent::init();
	}

}