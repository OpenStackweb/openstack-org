<?php
	class PrimaryLogoPage extends Page {
		static $db = array(
		);
		static $has_one = array(
	     );		
	     
		function getCMSFields() {
			$fields = parent::getCMSFields();
			
			return $fields;
		}   
			     				
		
	}

	class PrimaryLogoPage_Controller extends Page_Controller {
			
		function init() {
			// Populate the backURL session variable so login form sends us back to this page
			Session::set('BackURL', $this->Link());
			parent::init();
		}
		
		function BrandingMenu() {
			return TRUE;
		}
		
				
	}