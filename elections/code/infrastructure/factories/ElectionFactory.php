<?php

/**
 * Class ElectionFactory
 */
final class ElectionFactory implements IElectionFactory {

	/**
	 * @param int    $id
	 * @param DateTime $open_date
	 * @param DateTime $end_date
	 * @return IElection
	 */
	public function build($id, DateTime $open_date, DateTime $end_date)
	{
		$election = new Election();
		$election->ID             = $id;
		$election->ElectionsOpen  = $open_date->format('Y-m-d');
		$election->ElectionsClose = $end_date->format('Y-m-d');
		return $election;
	}
}