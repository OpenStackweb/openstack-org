<?php
/**
 * Class TeamInvitationEmailSender
 */
final class TeamInvitationEmailSender implements ITeamInvitationSender {

	/**
	 * @var ITeamInvitationRepository
	 */
	private $team_invitation_repository;

	/**
	 * @param ITeamInvitationRepository $team_invitation_repository
	 */
	public function __construct(ITeamInvitationRepository $team_invitation_repository){
		$this->team_invitation_repository = $team_invitation_repository;

	}
	/**
	 * @param ITeamInvitation $invitation
	 * @return void
	 */
	public function sendInvitation(ITeamInvitation $invitation)	{
		$invite_dto = $invitation->getInviteInfo();

		$email_to = $invite_dto->getEmail();
		//to avoid accidentally send undesired emails....
		if(defined('CCLA_DEBUG_EMAIL'))
			$email_to = CCLA_DEBUG_EMAIL;

		$email = EmailFactory::getInstance()->buildEmail(CCLA_TEAM_INVITATION_EMAIL_FROM, $email_to  , "You Have been Invited to Team ".$invitation->getTeam()->getName());

		$template_data = array(
			'FirstName'   => $invite_dto->getFirstName(),
			'LastName'    => $invite_dto->getLastName(),
			'TeamName'    => $invitation->getTeam()->getName(),
			'CompanyName' => $invitation->getTeam()->getCompany()->Name
		);

		if($invitation->isInviteRegisteredAsUser()){
			$email->setTemplate('TeamInvitation_RegisteredUser');
			do{
				$token = $invitation->generateConfirmationToken();
			} while ($this->team_invitation_repository->existsConfirmationToken($token));
			$template_data['ConfirmationLink'] = sprintf('%s/team-invitations/%s/confirm', Director::protocolAndHost(), $token);
		}
		else{
			$email->setTemplate('TeamInvitation_UnRegisteredUser');
			$template_data['RegistrationLink'] = sprintf('%s/join/register', Director::protocolAndHost());
		}

		$email->populateTemplate($template_data);

		$email->send();
	}
}