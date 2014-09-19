<?php

/**
 * Interface IElectionFactory
 */
interface IElectionFactory {

	/**
	 * @param int    $id
	 * @param DateTime $open_date
	 * @param DateTime $end_date
	 * @return IElection
	 */
	public function build($id, DateTime $open_date, DateTime $end_date);
} 