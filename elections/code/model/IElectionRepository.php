<?php

/**
 * Interface IElectionRepository
 */
interface IElectionRepository extends IEntityRepository {
	/**
	 * @param int $n
	 * @return IElection[]
	 */
	public function getLatestNElections($n);

	/**
	 * @param int $years
	 * @return IElection
	 */
	public function getEarliestElectionSince($years);
} 