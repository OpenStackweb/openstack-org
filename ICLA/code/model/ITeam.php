<?php

/**
 * Interface ITeam
 */
interface ITeam extends IEntity {
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $name
	 * @return void
	 */
	public function updateName($name);

	/**
	 * @return ICLAMember[]
	 */
	public function getMembers();

	/**
	 * @param ICLAMember $member
	 * @return void
	 */
	public function addMember(ICLAMember $member);

	/**
	 * @param ICLAMember $member
	 * @return void
	 */
	public function removeMember(ICLAMember $member);

	/**
	 * @return ITeamInvitation[]
	 */
	public function getInvitations();

	/**
	 * @return ITeamInvitation[]
	 */
	public function getUnconfirmedInvitations();

	/**
	 * @return ICLACompany
	 */
	public function getCompany();

	/**
	 * @param ICLAMember $member
	 * @return bool
	 */
	public function isInvite(ICLAMember $member);

	/**
	 * @param ICLAMember $member
	 * @return bool
	 */
	public function isMember(ICLAMember $member);

	/**
	 * @param ITeamInvitation $invitation
	 * @return void
	 */
	public function removeInvitation(ITeamInvitation $invitation);

	public function clearMembers();

	public function clearInvitations();

} 