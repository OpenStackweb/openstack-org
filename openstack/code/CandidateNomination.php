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

}