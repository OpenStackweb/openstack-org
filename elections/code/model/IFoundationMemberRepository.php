<?php

/**
 * Interface IFoundationMemberRepository
 */
interface IFoundationMemberRepository extends IEntityRepository {

	/**
	 * @param int                 $n
	 * @param int                 $limit
	 * @param int                 $offset
	 * @param IElectionRepository $election_repository
	 * @return int[]
	 */
	public function getMembersThatNotVotedOnLatestNElections($n, $limit, $offset, IElectionRepository $election_repository);


	/**
	 * @param string $first_name
	 * @param string $last_name
	 * @return IFoundationMember
	 */
	public function getByCompleteName($first_name, $last_name);
} 