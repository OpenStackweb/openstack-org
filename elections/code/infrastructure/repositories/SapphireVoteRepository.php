<?php

/**
 * Class SapphireVoteRepository
 */
final class SapphireVoteRepository extends SapphireRepository
implements IVoteRepository
{

	public function __construct(){
		parent::__construct(new Vote);
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