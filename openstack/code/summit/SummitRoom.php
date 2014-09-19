<?php
	
class SummitRoom extends DataObject {

	static $db = array(
		'Name' => 'Text',
		'Location' => 'Text'
	);
	
	static $has_one = array(
		'Summit' => 'Summit'
	);

}