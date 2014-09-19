<?php
	class OSLogoProgramPage extends Page {
		static $db = array(
		);
		static $has_one = array(
	     );
		static $has_many = array(
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

	class OSLogoProgramPage_Controller extends Page_Controller {

		public static $allowed_actions = array (
			'Form'
      	);	

		function init() {
			parent::init();
		}

		function Form() {
			$Form = new OSLogoProgramForm($this, 'Form');
			return $Form;
		}
			
	}