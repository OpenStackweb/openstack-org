<?php

/**
 * Interface ITeamInvitation
 */
interface ITeamInvitation extends IEntity {

	/**
	 * @return InviteInfoDTO
	 */
	public function getInviteInfo();

	/**
	 * @return bool
	 */
	public function isInviteRegisteredAsUser();

	/**
	 * @return ITeam
	 */
	public function getTeam();

	/**
	 * @return string
	 */
	public function generateConfirmationToken();

	/**
	 * @param string $token
	 * @return bool
	 * @throws InvalidHashInvitationException
	 * @throws InvitationAlreadyConfirmedException
	 */
	public function doConfirmation($token);

	/**
	 * @return ICLAMember
	 */
	public function getMember();

	public function updateInvite(ICLAMember $invite);
}