<?php
	
class SchedEvent extends DataObject {

	static $db = array(
		'event_key' => 'Varchar',
		'eventtitle' => 'Text',
		'event_start' => 'Datetime',
		'event_end' => 'Datetime',
		'event_type' => 'Varchar',
		'description' => 'Text',
		'speakers' => 'Text'
	);

	static $singular_name = 'Event';
	static $plural_name = 'Events';

	function Metadata() {
		return SchedEventMetadata::get()->filter('event_key',$this->event_key)->first();
	}

	function IsASpeaker($SpeakerID) {
		if(is_numeric($SpeakerID)) {

			$Speaker = SchedSpeaker::get()->byID($SpeakerID);

			// Check to see if the speaker is listed on this event
			if( $Speaker &&
				$Speaker->name && 
				strpos($this->speakers, $Speaker->name) !== FALSE ) 
			{
				return TRUE;
			}	
		}
	}

	function UploadedMedia() {

		$Metadata = $this->Metadata();

		if($Metadata && $Metadata->UploadedMediaID) {
			$File = File::get()->byID($Metadata->UploadedMediaID);
			return $File;
		}
	}
	
	function HostedMediaURL() {
		$Metadata = $this->Metadata();
		if($Metadata) return $Metadata->HostedMediaURL;
	}

	function isFile() {
		$Metadata = $this->Metadata();
		if($Metadata) return $Metadata->MediaType == 'File';
	}

	function HasAttachmentOrLink() {
		$Metadata = $this->Metadata();
		if($Metadata) return ($Metadata->MediaType || $Metadata->HostedMediaURL);
	}


}