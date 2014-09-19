<?php
/**
 * Defines the JobsHolder page type
 */
class BioPage extends Page {
   static $db = array(
   );
   static $has_one = array(
   );
   static $has_many = array(
   		'Bios' => 'Bio'
   );

	function getCMSFields() {
	   	$fields = parent::getCMSFields();

		$biosTable = new GridField('Bio', 'Bio', $this->Bios());
	  	   	$fields->addFieldToTab('Root.Bios',$biosTable);
	   	return $fields;
	}   
 
}
 
class BioPage_Controller extends Page_Controller {
	
	public function Children() {
		return Bio::get()->filter(array('BioPageID'=>$this->ID))->sort('LastName ASC');
	}

}