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
 * Class SapphireVoteRepository
 */
final class SapphireVoteRepository extends SapphireRepository
implements IVoteRepository
{

	public function __construct(){
		parent::__construct(new ElectionVote);
	}

	/**
	 * @param int   $foundation_member_id
	 * @param array $election_ids
	 * @return int
	 */
	public function getVotesCountByMemberAndElections($foundation_member_id, array $election_ids)
	{
		// TODO: Implement getVotesCountByMemberAndElections() method.
	}
}