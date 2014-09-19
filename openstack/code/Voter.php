<?php
	
class Voter extends DataObject {

	static $db = array(
	);
	
	static $has_one = array(
		'Member' => 'Member'
	);

	static $has_many = array(
		'SpeakerVotes' => 'SpeakerVote'
	);
	
	static $singular_name = 'Voter';
	static $plural_name = 'Voters';
	
}