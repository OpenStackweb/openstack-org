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
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class CandidateNomination
 */
class CandidateNomination
    extends DataObject
    implements ICandidateNomination {

	static $db = [];
	
	static $has_one = [
		'Member'    => 'Member', # Who made the nomination
		'Candidate' => 'Member', # Which candidate was nominated
		'Election'  => 'Election' # Which election the nomination was for
	];

	/*
    static $indexes = [
        'Nomination_Member_Election' => ['type'=>'unique', 'value'=>'MemberID,CandidateID,ElectionID']
    ];
	*/

	function getVotingMember() {
		return Member::get()->byID($this->MemberID);
	}

	function getNominee() {
		return Candidate::get()->filter('MemberID', $this->CandidateID)->first();
  	}

    /**
     * @param ICommunityMember $member
     * @throws Exception
     */
    function updateNominee(ICommunityMember $member){
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Candidate')->setTarget($member);
    }

    /**
     * @param ICommunityMember $member
     * @throws Exception
     */
    function updateVotingMember(ICommunityMember $member){
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Member')->setTarget($member);
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

        $already_exist_another_candidate = CandidateNomination::get()->filter([
                'ID:ExactMatch:not' => $this->ID,
                'ElectionID'        => $this->ElectionID,
                'MemberID'          => $this->MemberID,
                'CandidateID'       => $this->CandidateID
            ])->count() > 0;

        if ($already_exist_another_candidate) {
            return $valid->error('Already exist another candidate nomination with same candidate and proposer for this election!');
        }

        return $valid;
    }

}