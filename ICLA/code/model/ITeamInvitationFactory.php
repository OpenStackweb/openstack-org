<?php
/**
 * Interface ITeamInvitationFactory
 */
interface ITeamInvitationFactory {
	/**
	 * @param InvitationDTO $invitation_dto
	 * @return ITeamInvitation
	 */
	public function buildInvitation(InvitationDTO $invitation_dto);
} 