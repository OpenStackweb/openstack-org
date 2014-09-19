<?php

/**
 * Interface IElection
 */
interface IElection extends IEntity {
	/**
	 * @return DateTime
	 */
	public function startDate();
	/**
	 * @return DateTime
	 */
	public function endDate();
} 