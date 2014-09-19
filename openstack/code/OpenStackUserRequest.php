<?php
	
class OpenStackUserRequest extends DataObject {

	static $db = array(
		'Name' => 'Text',
		'Company' => 'Text',
		'Email' => 'Text'
	);
	
	static $has_one = array(
	);
	
	static $singular_name = 'OpenStack User Request';
	static $plural_name = 'OpenStack User Requests';
	
}