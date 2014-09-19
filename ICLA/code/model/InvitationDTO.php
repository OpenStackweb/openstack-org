<?php

/**
 * Class InvitationDTO
 */
final class InvitationDTO extends InviteInfoDTO {

	/**
	 * @var ITeam
	 */
	private $team;

	/**
	 * @var ICLAMember
	 */
	private $member;

	/**
	 * @param string     $first_name
	 * @param string     $last_name
	 * @param string     $email
	 * @param ITeam      $team
	 * @param ICLAMember $member
	 */
	public function __construct($first_name, $last_name, $email, ITeam $team, ICLAMember $member = null){
		parent::__construct($first_name, $last_name, $email);
		$this->team       = $team;
		$this->member     = $member;
	}

	/*
	 * @return ITeam
	 */
	public function getTeam(){
		return $this->team;
	}

	/**
	 * @return ICLAMember
	 */
	public function getMember(){
		return $this->member;
	}
} 