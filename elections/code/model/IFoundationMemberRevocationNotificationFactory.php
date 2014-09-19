<?php

/**
 * Interface IFoundationMemberRevocationNotificationFactory
 */
interface IFoundationMemberRevocationNotificationFactory {

	/**
	 * @param IFoundationMember $foundation_member
	 * @param IElection         $last_election
	 * @return IFoundationMemberRevocationNotification
	 */
	public function build(IFoundationMember $foundation_member, IElection $last_election);
} 