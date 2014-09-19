<?php
	
class TalkVote extends DataObject {

	static $db = array(
		'VoteValue' => 'Int',
		'Note' => 'Text',
		'IP' => 'Varchar'
	);
	
	static $has_one = array(
		'Voter' => 'Voter',
		'Talk' => 'Talk'
	);
	
	static $singular_name = 'Vote';
	static $plural_name = 'Votes';


	function PresentationTitle() {
		$presentation = SpeakerSubmission::get()->byID($this->SpeakerSubmissionID);
		return $presentation->PresentationTitle;
	}
	
}