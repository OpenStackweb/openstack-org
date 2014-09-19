<?php
	
class SummitTrackChair extends DataObject {
	
	static $has_one = array(
		'Member' => 'Member',
		'Category' => 'SummitCategory',
		'HasBeenEmailed' => 'Boolean'
	);

}