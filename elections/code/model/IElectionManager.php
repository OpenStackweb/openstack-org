<?php
/**
 * Copyright 2018 OpenStack Foundation
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

interface IElectionManager
{
    /**
     * @param $filename
     * @param $election_id
     * @return mixed
     */
    function ingestVotersForElection($filename, $election_id);


    /**
     * @param int $member_id
     * @throws EntityValidationException
     * @throws NotFoundEntityException
     * @return bool
     */
    public function isValidNomination($member_id);

    /**
     * @param int $member_id
     * @param IMessageSenderService $nomination_email_sender
     * @return CandidateNomination
     */
    function nominateMemberOnCurrentElection($member_id, IMessageSenderService $nomination_email_sender);

    /**
     * @param Member $member
     * @param Election $election
     * @param array $data
     * @return Candidate
     * @throws EntityValidationException
     */
    public function registerCandidate(Member $member, Election $election, array $data);
}