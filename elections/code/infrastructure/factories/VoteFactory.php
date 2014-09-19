<?php

/**
 * Class VoteFactory
 */
final class VoteFactory implements IVoteFactory {

	/**
	 * @param IElection         $election
	 * @param IFoundationMember $voter
	 * @return IVote
	 */
	public function buildVote(IElection $election, IFoundationMember $voter)
	{
		$vote = new Vote;
		$vote->ElectionID = $election->getIdentifier();
		$vote->VoterID    = $voter->getIdentifier();
		return $vote;
	}
}