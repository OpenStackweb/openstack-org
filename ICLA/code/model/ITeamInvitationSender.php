<?php

/**
 * Interface ITeamInvitationSender
 */
interface ITeamInvitationSender {

	/**
	 * @param ITeamInvitation $invitation
	 * @return void
	 */
	public function sendInvitation(ITeamInvitation $invitation);
} 