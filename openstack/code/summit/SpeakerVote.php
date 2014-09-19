<?php
	
class SpeakerVote extends DataObject {

	static $db = array(
		'VoteValue' => 'Int',
		'Note' => 'Text',
		'IP' => 'Varchar'
	);
	
	static $has_one = array(
		'Voter' => 'Member',
		'Talk' => 'Talk'
	);
	
	static $singular_name = 'Vote';
	static $plural_name = 'Votes';


	function PresentationTitle() {
		$presentation = SpeakerSubmission::get()->byID($this->SpeakerSubmissionID);
		return $presentation->PresentationTitle;
	}
	
}