<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissioMoreThanTenns and
 * limitations under the License.
 **/
class Candidate extends DataObject implements ICandidate
{

	static $db = [
		'HasAcceptedNomination'   => 'Boolean',
		'IsGoldMemberCandidate'   => 'Boolean',
        // Q and A (Candidate's answer to the application question)
        'RelationshipToOpenStack' => 'HTMLText',
        'Experience'              => 'HTMLText',
        'BoardsRole'              => 'HTMLText',
        'TopPriority'             => 'HTMLText',
	];

	static $has_one = [
		'Election' => 'Election', // Used to track which election this candidate belongs to.
		'Member'   => 'Member' // Links the candidate to the member record
	];

    /*static $indexes = [
        'Candidate_Election' => ['type'=>'unique', 'value'=>'MemberID,ElectionID']
    ];*/

	static $singular_name = 'Candidate';
	static $plural_name   = 'Candidates';

	static $defaults = [

	];

	// Return the number of nominations for this candidate
	function countNominations()
	{
		$current_election = $this->Election();
		if(!$current_election) return 0;
		$Nominations = $current_election->CandidateNominations("`CandidateID` = " . $this->MemberID);
		return $Nominations->Count();
	}

    /**
     * @return bool
     */
	function hasBeenNominatedByMe()
	{
	  	if ($LoggedInMemberID = Member::currentUserID()) {
			$current_election = $this->Election();
			if(!$current_election) return false;
			return $current_election->CandidateNominations("`CandidateID` = " . $this->MemberID . " AND `MemberID` = " . $LoggedInMemberID);
		}
	}

	// Return if this candidate has received more than 10 nominations
	function MoreThanTen()
	{
		$Nominations = $this->Election()->CandidateNominations("`CandidateID` = " . $this->MemberID);
		return ($Nominations->Count() >= 10);
	}

	// Return if this candidate has received more than 10 nominations
	function LessThanTen()
	{
		$Nominations = $this->Election()->CandidateNominations("`CandidateID` = " . $this->MemberID);
		return ($Nominations->Count() < 10);
	}

	// Return the nominations for this candidate
	function getNominations()
	{
		return $this->Election()->CandidateNominations("`CandidateID` = " . $this->MemberID);
	}

    /**
     * @param ICommunityMember $member
     * @return void
     */
    public function updateMember(ICommunityMember $member){
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'Member')->setTarget($member);
    }

    /**
     * @return int
     */
    public function getIdentifier(){
        return (int)$this->getField('ID');
    }

    protected function validate()
    {

        $valid = parent::validate();
        if (!$valid->valid()) {
            return $valid;
        }

        $already_exist_another_candidate = Candidate::get()->filter([
            'ID:ExactMatch:not' => $this->ID,
            'ElectionID'        => $this->ElectionID,
            'MemberID'          => $this->MemberID
        ])->count() > 0;

        if ($already_exist_another_candidate) {
            return $valid->error('Already exist another candidate with same member for this election!');
        }

        return $valid;
    }
}