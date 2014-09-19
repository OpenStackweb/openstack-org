<?php
	
class Bio extends DataObject {

	static $db = array(
		'FirstName' => 'Text',
		'LastName' => 'Text',
		'Email' => 'Text',
		'JobTitle' => 'Text',
		'Company' => 'Text',
		'Bio' => 'HTMLText',
		'DisplayOnSite' => 'Boolean',
		'Role' => 'Text'
	);

	Static $defaults = array('DisplayOnSite' => TRUE);
	
	static $has_one = array(
		'Photo' => 'BetterImage',
		'BioPage' => 'BioPage'
	);

	static $summary_fields = array( 
	      'FirstName' => 'First Name', 
	      'LastName' => 'Last Name',
	      'Email' => 'Email'
	 );	
	
	static $singular_name = 'Bio';
	static $plural_name = 'Bios';

	function getCMSFields() {
		$photo = new CustomUploadField('Photo', 'Photo');
		$photo->setAllowedFileCategories('image');
		$fields = new FieldList (
			new TextField('FirstName','First Name'),
			new TextField('LastName','Last Name'),
			new TextField('Email','Email'),
			new TextField('Role','Role / Position For This OpenStack Group (if any)'),			
			new TextField('JobTitle','Job Title'),
			new TextField('Company','Company'),
			new HtmlEditorField('Bio','Brief Bio'),
			new CheckboxField ('DisplayOnSite','Inlcude this bio on openstack.org'),
			$photo
		);
		return $fields;
	}

}