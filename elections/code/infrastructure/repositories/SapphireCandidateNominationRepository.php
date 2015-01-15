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
 * Class SapphireCandidateNominationRepository
 */
final class SapphireCandidateNominationRepository
    extends SapphireRepository
    implements ICandidateNominationRepository {

    public function __construct(){
        parent::__construct(new CandidateNomination());
    }

    public function getNominationsByVotingMember($member_id,  $offset = 0, $limit = 10) {
        $query = new QueryObject(new CandidateNomination);
        $query->addAlias(QueryAlias::create('Member'));
        $query->addAddCondition(QueryCriteria::equal('Member.ID', $member_id));
        return $this->getAll($query,$offset,$limit);
    }

    public function getNominationsByNominee($member_id,  $offset = 0, $limit = 10) {
        $query = new QueryObject(new CandidateNomination);
        $query->addAlias(QueryAlias::create('Candidate'));
        $query->addAddCondition(QueryCriteria::equal('Member.ID', $member_id));
        return $this->getAll($query,$offset,$limit);
    }
}