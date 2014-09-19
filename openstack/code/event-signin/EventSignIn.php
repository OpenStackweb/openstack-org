<?php
 
class EventSignIn extends DataObject {
   // Define what variables this data object has
   static $db = array(
	'EmailAddress' => 'Text',
	'FirstName' => 'Text',
	'LastName' => 'Text'
   );
   
   // Create a relationship between the data object and its parent page. This is needed especially for the DOM's edit windows to work.
	static $has_one = array (
		'SigninPage' => 'SigninPage'
	);

	//Define fields to show in the DOM list view table
	static $summary_fields = array(
		// 'field name' => 'column label'
		'EmailAddress' => 'Email Address',
		'FirstName' => 'First Name',
		'LastName' => 'Last Name'
	);
	
	//Define fields to show in the popup editor window
	public function getCMSFields()
	{
		return new FieldList(
			new TextField('FirstName'),
			new TextField('LastName'),
			new TextField('EmailAddress')
		);
	}

}