<?php
/**
 * Defines the JobsHolder page type
 */
class StartPageHolder extends Page {
   static $db = array(
   );
   static $has_one = array(
   );
 
   static $allowed_children = array('StartPage');
      
}
 
class StartPageHolder_Controller extends Page_Controller {
	
	function init() {
	    parent::init();
	} 
	
  function isOverview() {
      return TRUE;
  }

  function StartOverview() {
    return $this->Content;
  }

}