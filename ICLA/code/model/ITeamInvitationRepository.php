<?php

/**
 * Interface ITeamInvitationRepository
 */
interface ITeamInvitationRepository extends IEntityRepository {
	/**
	 * @param string $token
	 * @return bool
	 */
	public function existsConfirmationToken($token);

	/**
	 * @param string $token
	 * @return ITeamInvitation
	 */
	public function findByConfirmationToken($token);

	/**
	 * @param string $email
	 * @param bool $all
	 * @return ITeamInvitation[]
	 */
	public function findByInviteEmail($email, $all = false);

	/**
	 * @param string $email
	 * @param ITeam $team
	 * @return ITeamInvitation
	 */
	public function findByInviteEmailAndTeam($email, ITeam $team);
} 