<?php
	
class SummitRegCode extends DataObject {
	
	static $db = array(
		'Code' => 'Varchar',
		'Type' => 'Varchar'
	);

	static $has_one = array(
		'Member' => 'Member'
	);

}