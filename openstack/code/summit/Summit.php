<?php
	
class Summit extends DataObject {

	static $db = array(
		'Name' => 'Varchar(255)',
		'Location' => 'Varchar(255)',
		'StartDate' => 'Date',
		'EndDate' => 'Date',
		'AcceptSubmissionsStartDate' => 'Date',
		'AcceptSubmissionsEndDate' => 'Date',
		'VoteForSubmissionsStartDate' => 'Date',
		'VoteForSubmissionsEndDate' => 'Date',
	);
	
	static $has_many = array(
		'Talks' => 'Talk',
		'Tracks' => 'SummitTrack',
		'Rooms' => 'SummitRoom',
		'TimeSlots' => 'SummitTimeSlot',
		'SummitCategories' => 'SummitCategory'
	);

	function TalksByMemberID($memberID) {

		$SpeakerList = new ArrayList();

		// Pull any talks that belong to this Summit and are owned by member
		$talksMemberOwns = $this->Talks("`OwnerID` = ".$memberID." AND `SummitID` = ".$this->ID);
		$SpeakerList->merge($talksMemberOwns);

		// Now pull any talks that belong to this Summit and the member is listed as a speaker
		$speaker = Speaker::get()->filter('memberID',$memberID)->first();
		if($speaker) {
			$talksMemberIsASpeaker = $speaker->TalksBySummitID($this->ID);

			// Now merge and de-dupe the lists
			$SpeakerList->merge($talksMemberIsASpeaker);
			$SpeakerList->removeDuplicates('ID');
		}

		return $SpeakerList;
	}

	public function CurrentSummitID() {
		// todo: make this a property editable in the CMS
		return 3;
	}

	public function CurrentSummit() {
		return DataObject::get_by_id('Summit',$this->CurrentSummitID());
	}

}