<?php

/**
 * Class TeamInvitationFactory
 */
final class TeamInvitationFactory implements ITeamInvitationFactory{

	/**
	 * @param InvitationDTO $invitation_dto
	 * @return ITeamInvitation
	 */
	public function buildInvitation(InvitationDTO $invitation_dto)
	{
		$invitation = new TeamInvitation();

		$invitation->FirstName = $invitation_dto->getFirstName();
		$invitation->LastName  = $invitation_dto->getLastName();
		$invitation->Email     = $invitation_dto->getEmail();
		$invitation->setTeam($invitation_dto->getTeam());
		$member = $invitation_dto->getMember();
		if($member){
			$invitation->setMember($member);
		}
		return $invitation;
	}
}