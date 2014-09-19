<?php

/**
 * Interface IFoundationMember
 */
interface IFoundationMember extends IEntity {

	const FoundationMemberGroupSlug = 'foundation-members';
	const CommunityMemberGroupSlug = 'community-members';
	/**
	 * @return void
	 */
	public function convert2SiteUser();

	/**
	 * @return bool
	 */
	public function isFoundationMember();

	/**
	 * @return void
	 */
	public function upgradeToFoundationMember();

	/**
	 * @param int $latest_election_id
	 * @return bool
	 */
	public function hasPendingRevocationNotifications($latest_election_id);

	public function resign();

} 