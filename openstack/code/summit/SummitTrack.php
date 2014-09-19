<?php
	
class SummitTrack extends DataObject {

	static $db = array(
		'Name' => 'Text'
	);
	
	static $has_one = array(
		'Summit' => 'Summit'
	);

	static $has_many = array(
		'Talks' => 'Talk',
		'Chairs' => 'SummitTrackChair',
		'TimeSlots' => 'SummitTimeSlots'
	);

}