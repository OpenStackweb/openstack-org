<?php

/**
 * Class RevocationNotificationFactory
 */
final class RevocationNotificationFactory implements IFoundationMemberRevocationNotificationFactory {

	/**
	 * @param IFoundationMember $foundation_member
	 * @param IElection         $last_election
	 * @return IFoundationMemberRevocationNotification
	 */
	public function build(IFoundationMember $foundation_member, IElection $last_election)
	{
		$notification                  = new FoundationMemberRevocationNotification;
		$notification->RecipientID     = $foundation_member->getIdentifier();
		$notification->LastElectionID  = $last_election->getIdentifier();
		return $notification;
	}
}