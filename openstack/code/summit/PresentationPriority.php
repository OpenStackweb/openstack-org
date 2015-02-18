<?php

class PresentationPriority extends DataObject
{

	private static $db = array (
		'Priority' => 'Int'
	);


	private static $has_one = array (
		'Talk' => 'Talk',
		'Member' => 'Member'
	);


	private static $indexes = array(
		'Member_Priority' => array(
			'type' => 'index',
			'value' => '"MemberID","Priority"'
		),
	);

}