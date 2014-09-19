<?php

/**
 * Interface IVote
 */
interface IVote extends IEntity {
	/**
	 * @return IFoundationMember
	 */
	public function voter();

	/**
	 * @return IElection
	 */
	public function election();

	/**
	 * @return DateTime
	 */
	public function date();
} 