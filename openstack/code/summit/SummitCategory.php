<?php
	
class SummitCategory extends DataObject {

	static $db = array(
		'Name' => 'Text',
		'NumSessions' => 'Int',
		'ShortPhrase' => 'Text',
		'Description' => 'HTMLText'
	);
	
	static $has_one = array(
		'Summit' => 'Summit'
	);

	static $has_many = array(
		'SummitSelectedTalkLists' => 'SummitSelectedTalkList',
		'SummitTrackChairs' => 'SummitTrackChair',
		'Talks' => 'Talk'
	);

	function MemberIsTrackChair() {
		$MemberID = Member::currentUser()->ID;
        if ($this->SummitTrackChairs('MemberID = '.$MemberID)->Count() || Permission::check("ADMIN")) return TRUE;
	}

	function NumberOfAvailableTalks() {
		return $this->Talks('`MarkedToDelete` = 0')->Count();
	}

}
