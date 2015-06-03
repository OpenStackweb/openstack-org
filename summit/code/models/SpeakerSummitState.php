<?php


class SpeakerSummitState extends DataObject {

	private static $db = array (
		'Event' => 'Varchar',
		'Notes' => 'Text'
	);


	private static $has_one = array (
		'Summit' => 'Summit',
		'Member' => 'Member'
	);
}