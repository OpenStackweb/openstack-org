<?php

class PresentationPriority extends DataObject
{

	private static $db = array (
		'Priorities' => 'Text'
	);


	private static $has_one = array (
		'Summit' => 'Summit',		
	);

	private static $has_many = array (
		'Members' => 'Member'
	);

	public function getPriorityList () {
		return Convert::json2array($this->Priorities);
	}
}