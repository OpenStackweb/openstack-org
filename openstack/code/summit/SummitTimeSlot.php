<?php
	
class SummitTimeSlot extends DataObject {

	static $db = array(
		'Start' => 'Datetime',
		'End' => 'Datetime'
	);
	
	static $has_one = array(
		'Talk' => 'Talk',
		'Track' => 'Track',
		'Summit' => 'Summit',
		'Room' => 'SummitRoom'
	);

}