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


}