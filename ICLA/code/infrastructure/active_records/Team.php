<?php

/**
 * Class Team
 */
final class Team
	extends DataObject
	implements ITeam
{

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Name' => 'Text',
	);

	static $has_one = array(
		'Company' => 'Company',
	);

	static $has_many = array(
		'Invitations' => 'TeamInvitation',
	);

	static $many_many = array(
		'Members' => 'Member',
	);

	//Administrators Security Groups
	static $many_many_extraFields = array(
		'Members' => array(
			'DateAdded' => "SS_DateTime",
		),
	);

	public static $defaults = array(
		"DateAdded" => 'now()',
	);


	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return (string)$this->getField('Name');
	}

	/**
	 * @return ICLAMember[]
	 */
	public function getMembers()
	{
		return AssociationFactory::getInstance()->getMany2ManyAssociation($this , 'Members')->toArray();
	}

	/**
	 * @param ICLAMember $member
	 * @return void
	 */
	public function addMember(ICLAMember $member)
	{
		AssociationFactory::getInstance()->getMany2ManyAssociation($this , 'Members')->add($member, array('DateAdded'=> SS_Datetime::now()->Rfc2822()));
	}

	/**
	 * @param ICLAMember $member
	 * @return void
	 */
	public function removeMember(ICLAMember $member)
	{
		AssociationFactory::getInstance()->getMany2ManyAssociation($this , 'Members')->remove($member);
	}

	/**
	 * @return ITeamInvitation[]
	 */
	public function getInvitations(){
		return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Invitations')->toArray();
	}


	/**
	 * @return ITeamInvitation[]
	 */
	public function getUnconfirmedInvitations()
	{
		$query = new QueryObject();
		$query->addAddCondition(QueryCriteria::equal('IsConfirmed', 0));
		return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Invitations' , $query)->toArray();
	}

	/**
	 * @return ICLACompany
	 */
	public function getCompany()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Company')->getTarget();
	}

	/**
	 * @param ICLAMember $member
	 * @return bool
	 */
	public function isInvite(ICLAMember $member)
	{
		$member_id  = $member->getIdentifier();
		$res = $this->Invitations(" MemberID = {$member_id} ");
		return $res->Count() > 0;
	}

	/**
	 * @param ICLAMember $member
	 * @return bool
	 */
	public function isMember(ICLAMember $member)
	{
		$member_id  = $member->getIdentifier();
		$res = $this->Members(" MemberID = {$member_id} ");
		return $res->Count() > 0;
	}

	/**
	 * @param ITeamInvitation $invitation
	 * @return void
	 */
	public function removeInvitation(ITeamInvitation $invitation){
		AssociationFactory::getInstance()->getOne2ManyAssociation($this , 'Invitations')->remove($invitation);
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function updateName($name)
	{
		$this->setField('Name', $name);
	}

	public function clearMembers(){
		AssociationFactory::getInstance()->getMany2ManyAssociation($this , 'Members')->removeAll();
	}

	public function clearInvitations(){
		AssociationFactory::getInstance()->getOne2ManyAssociation($this , 'Invitations')->removeAll();
	}
}