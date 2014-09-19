<?php
	
class CandidateNomination extends DataObject {

	static $db = array(
	);
	
	static $has_one = array(
		'Member'    => 'Member', # Who made the nomination
		'Candidate' => 'Member', # Which candidate was nominated
		'Election'  => 'ElectionPage' # Which election the nomination was for
	);

	function getVotingMember() {
		return Member::get()->byID($this->MemberID);
	}

	function getNominee() {
		return Candidate::get()->filter('MemberID',$this->CandidateID)->first();
	}

}