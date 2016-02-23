<?php

class PresentationRandomVotingList extends DataObject
{

	private static $db = array (
		'SequenceJSON' => 'Text'
	);


	private static $has_one = array (
		'Summit' => 'Summit',		
	);

	private static $has_many = array (
		'Members' => 'Member'
	);

	public function getPriorityList () {
		return Convert::json2array($this->SequenceJSON);
	}

	public function setSequence ($list) {
		$this->SequenceJSON = Convert::array2json($list);
	}
}