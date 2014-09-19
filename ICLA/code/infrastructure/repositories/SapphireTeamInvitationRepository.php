<?php

/**
 * Class SapphireTeamInvitationRepository
 */
final class SapphireTeamInvitationRepository
	extends SapphireRepository
	implements ITeamInvitationRepository
{

	public function __construct(){
		parent::__construct(new TeamInvitation);
	}

	/**
	 * @param string $token
	 * @return bool
	 */
	public function existsConfirmationToken($token)
	{
		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('ConfirmationHash',TeamInvitation::HashConfirmationToken($token)));
		return  !is_null( $this->getBy($query));
	}

	/**
	 * @param string $token
	 * @return ITeamInvitation
	 */
	public function findByConfirmationToken($token)
	{
		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('ConfirmationHash',TeamInvitation::HashConfirmationToken($token)));
		return $this->getBy($query);
	}

	/**
	 * @param string $email
	 * @param bool $all
	 * @return ITeamInvitation[]
	 */
	public function findByInviteEmail($email, $all = false)
	{
		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('Email',$email));
		if(!$all)
		$query->addAddCondition(QueryCriteria::isNull('ConfirmationHash'));
		list($res, $size) =  $this->getAll($query,0,1000);
		return $res;
	}

	/**
	 * @param string $email
	 * @param ITeam $team
	 * @return ITeamInvitation
	 */
	public function findByInviteEmailAndTeam($email, ITeam $team){

		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal('Email',$email));
		$query->addAddCondition(QueryCriteria::equal('TeamID',$team->getIdentifier()));
		return $this->getBy($query);
	}
}