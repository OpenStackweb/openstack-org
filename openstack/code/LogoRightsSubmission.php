<?php
 
class LogoRightsSubmission extends DataObject {
   // Define what variables this data object has
   static $db = array(
	'Name' => 'Text',
	'Email' => 'Text',
	'PhoneNumber' => 'Text',
	'ProductName' => 'Text',
	'CompanyName' => 'Text',
	'Website' => 'Text',
	'StreetAddress' => 'Text',
	'State' => 'Text',
	'City' => 'Text',
	'Country' => 'Text',
	'Zip' => 'Text',
	'BehalfOfCompany' => 'Boolean'
   );
   
   // Create a relationship between the data object and its parent page. This is needed especially for the DOM's edit windows to work.
	static $has_one = array (
		'LogoRightsPage' => 'LogoRightsPage'
	);

	//Define fields to show in the DOM list view table
	static $summary_fields = array(
		// 'field name' => 'column label'
		'Name' => 'Name',  
		'Email' => 'Email'
	);
	
	//Define fields to show in the popup editor window
	public function getCMSFields()
	{
		return new FieldList(
			new TextField('Name', 'Name'),
			new EmailField('Email', 'Email'),
			new TextField('PhoneNumber', 'Phone Number'),
			new TextField('ProductName', 'Product Name'),
			new TextField('CompanyName', 'Company Name'),
			new TextField('Website', 'Company Website (URL)'),
			new TextAreaField('StreetAddress', 'Street Address', 2, 20),
			new TextField('City', 'City'),
			new TextField('State', 'State'),
			new TextField('Zip', 'Zip / Postal Code'),
			new TextField('Country', 'Country'),
			new CheckboxField('BehalfOfCompany','This submission is on behalf of a company')
		);
	}

}