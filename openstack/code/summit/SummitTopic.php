<?php
	
class SummitTopic extends DataObject {

	static $db = array(
		'Location' => 'Varchar(255)',
		'Time' => 'SS_Datetime'
	);
	
	static $has_one = array(
		'Topic' => 'Topic',
		'Summit' => 'Summit'
	);

	static $has_many = array(
		'Talks' => 'Talk',
		'Chairs' => 'Member'
	);

	static $summary_fields = array( 
		'Topic.Name' => 'Name', 
		'Location' => 'Location',
		'Summit.Name' => 'Summit' 
	);

}