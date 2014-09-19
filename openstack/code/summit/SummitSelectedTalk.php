<?php
	
class SummitSelectedTalk extends DataObject {

	static $db = array(
		'Order' => 'Int'
	);
	
	static $has_one = array(
		'SummitSelectedTalkList' => 'SummitSelectedTalkList',
		'Talk' => 'Talk',
		'Member' => 'Member'
	);

	function TalkPosition() {

		$Talks = SummitSelectedTalk::get()->filter(array('SummitSelectedTalkListID' => $this->SummitSelectedTalkList()->ID, 'Order:not' => 0))->sort('Order','ASC');
		$TalkPosition = 0;

		$counter = 1;

		if($Talks) {
			foreach($Talks as $Talk) {
				if ($Talk->ID == $this->ID) {
					$TalkPosition = $counter;
				}
				$counter = $counter + 1;
			}
		}

		$Talks = SummitSelectedTalk::get()->filter(array('SummitSelectedTalkListID' => $this->SummitSelectedTalkList()->ID, 'Order' => 0))->sort('Order','ASC');
		
		if($Talks) {
			foreach($Talks as $Talk) {
				if ($Talk->ID == $this->ID) {
					$TalkPosition = $counter;
				}
				$counter = $counter + 1;
			}
		}


		return $TalkPosition;

	}

	function IsAlternate() {
		$TalkList = $this->SummitSelectedTalkList();
		$currentNum = $TalkList->SummitSelectedTalks()->Count();
		$maxNum = $this->Talk()->SummitCategory()->NumSessions;

		if($currentNum > $maxNum && ($this->TalkPosition() > $maxNum)) return TRUE;

	}

}