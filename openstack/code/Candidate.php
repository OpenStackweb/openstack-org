<?php

class Candidate extends DataObject
{

	static $db = array(
		'Nominated' => 'Boolean',
		'HasAcceptedNomination' => 'Boolean',
		'RelationshipToOpenStack' => 'HTMLText', // Candidate's answer to the application question
		'Experience' => 'HTMLText',
		'BoardsRole' => 'HTMLText',
		'TopPriority' => 'HTMLText',
		'IsGoldMemberCandidate' => 'Boolean'
	);

	static $has_one = array(
		'Election' => 'ElectionPage', // Used to track which election this candidate belongs to.
		'Member' => 'Member' // Links the candidate to the member record
	);


	static $singular_name = 'Candidate';
	static $plural_name = 'Candidates';

	static $defaults = array(
		"Nominated" => 1,
	);

	function CurrentElection()
	{
		// Load the election system
		$Elections = ElectionSystem::get()->first();
		if(!$Elections) return false;
		return $Elections->CurrentElection();
	}

	// Return the number of nominations for this candidate
	function countNominations()
	{
		$current_election = $this->CurrentElection();
		if(!$current_election) return 0;

		$Nominations = $current_election->CandidateNominations("`CandidateID` = " . $this->MemberID);
		return $Nominations->Count();
	}

	// Return whether the logged-in candidate has nominated this person in the current election
	function hasNominated()
	{
		if ($LoggedInMemberID = Member::currentUserID()) {
			$current_election = $this->CurrentElection();
			if(!$current_election) return false;
			return $current_election->CandidateNominations("`CandidateID` = " . $this->MemberID . " AND `MemberID` = " . $LoggedInMemberID);
		}
	}

	// Return if this candidate has received more than 10 nominations
	function MoreThanTen()
	{
		$current_election = $this->CurrentElection();
		if(!$current_election) return false;
		$Nominations = $current_election->CandidateNominations("`CandidateID` = " . $this->MemberID);
		return ($Nominations->Count() >= 10);
	}

	// Return if this candidate has received more than 10 nominations
	function LessThanTen()
	{
		$current_election = $this->CurrentElection();
		if(!$current_election) return false;
		$Nominations = $current_election->CandidateNominations("`CandidateID` = " . $this->MemberID);
		return ($Nominations->Count() < 10);
	}

	// Return the nominations for this candidate
	function getNominations()
	{
		$current_election = $this->CurrentElection();
		if(!$current_election) return false;
		return $current_election->CandidateNominations("`CandidateID` = " . $this->MemberID);
	}

}