<?php

/**
 * Interface IVoteRepository
 */
interface IVoteRepository extends IEntityRepository {
	/**
	 * @param int   $foundation_member_id
	 * @param array $election_ids
	 * @return int
	 */
	public function getVotesCountByMemberAndElections($foundation_member_id, array $election_ids);
} 