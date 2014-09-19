<?php

interface IVoteFactory {
	/**
	 * @param IElection         $election
	 * @param IFoundationMember $voter
	 * @return IVote
	 */
	public function buildVote(IElection $election, IFoundationMember $voter);
} 