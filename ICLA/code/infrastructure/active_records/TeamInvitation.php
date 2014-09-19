<?php
/**
 * Class TeamInvitation
 */
final class TeamInvitation
	extends DataObject
	implements ITeamInvitation
{

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Email'            => 'Text',
		'FirstName'        => 'Text',
		'LastName'         => 'Text',
		'ConfirmationHash' => 'Text',
		'IsConfirmed'      => 'Boolean',
		'ConfirmationDate' => 'SS_Datetime',
	);

	static $has_one = array(
		'Team'   => 'Team',
		'Member' => 'Member',
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return InviteInfoDTO
	 */
	public function getInviteInfo()	{
		return new InviteInfoDTO((string)$this->getField('FirstName'), (string)$this->getField('LastName'), (string)$this->getField('Email'));
	}

	/**
	 * @return bool
	 */
	public function isInviteRegisteredAsUser()
	{
		$member = $this->getMember();
		return $member && $member->getIdentifier() > 0 ;
	}

	/**
	 * @return ITeam
	 */
	public function getTeam()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Team')->getTarget();
	}

	public function setTeam(ITeam $team){
		AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Team')->setTarget($team);
	}


	/**
	 * @return ICLAMember
	 */
	public function getMember()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Member')->getTarget();
	}

	public function setMember(ICLAMember $member){
		AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Member')->setTarget($member);
	}

	/**
	 * @return string
	 */
	public function generateConfirmationToken() {
		$generator = new RandomGenerator();
		$token     = $generator->randomToken();
		$hash      = self::HashConfirmationToken($token);
		$this->setField('ConfirmationHash',$hash);
		return $token;
	}

	/**
	 * @param string $token
	 * @return bool
	 * @throws InvalidHashInvitationException
	 * @throws InvitationAlreadyConfirmedException
	 */
	public function doConfirmation($token)
	{
		$original_hash = $this->getField('ConfirmationHash');
		if($this->IsConfirmed) throw new InvitationAlreadyConfirmedException;
		if(self::HashConfirmationToken($token) === $original_hash){
			$this->IsConfirmed      = true;
			$this->ConfirmationDate = SS_Datetime::now()->Rfc2822();
			return true;
		}
		throw new InvalidHashInvitationException;
	}

	public static function HashConfirmationToken($token){
		return md5($token);
	}

	public function updateInvite(ICLAMember $invite)
	{
		$this->setMember($invite);
	}
}