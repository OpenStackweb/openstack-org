<?php

class LegalDocumentsHolder extends Page {
   static $db = array(
   );
   static $has_one = array(
   );
 
   static $defaults = array( 
      "ShowInMenus" => 0, 
   );
   
   static $allowed_children = array('LegalDocumentPage');
   /** static $icon = "icon/path"; */
      
}
 
class LegalDocumentsHolder_Controller extends Page_Controller {
	
	function init() {
	    parent::init();
	}
}