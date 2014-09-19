<?php
	
class SchedSpeakerEmailLog extends DataObject {

	static $db = array(
		'email' => 'Varchar'
	);

	function BeenEmailed($email) {
		$BeenEmailed = SchedSpeakerEmailLog::get()->filter('email',$email);
		return $BeenEmailed->count() > 0 ;
	}

	function addSpeaker($email) {
		$Speaker = new SchedSpeakerEmailLog();
		$Speaker->email = $email;
		if($email) $Speaker->write();
	}

	
}