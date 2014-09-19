<?php

/**
 * Class ICLACompanyDecorator
 */
class ICLACompanyDecorator
	extends DataExtension
{
	//Add extra database fields

	private static $db = array(
		'CCLASigned' => 'Boolean',
		'CCLADate'   => 'SS_Datetime',
	);

	private static $defaults = array(
		'CCLASigned' => FALSE,
	);


	private static $has_many = array(
		'Teams' => 'Team'
	);


	/**
	 * @return void
	 */
	public function signICLA()
	{
		$this->owner->setField('CCLASigned',true);
		$this->owner->setField('CCLADate',  SS_Datetime::now()->Rfc2822());
	}

	/**
	 * @return void
	 */
	public function unsignICLA()
	{
		$this->owner->setField('CCLASigned',false);
		$this->owner->setField('CCLADate', null);
	}

	/**
	 * @return bool
	 */
	public function isICLASigned()
	{
		return (bool)$this->owner->getField('CCLASigned');
	}

	/**
	 * @return Datetime
	 */
	public function ICLASignedDate()
	{
		$ss_datetime = $this->owner->getField('CCLADate');
		return new DateTime($ss_datetime);
	}

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->owner->getField('ID');
	}


	/**
	 * @return ITeam[]
	 */
	public function Teams()
	{
		return AssociationFactory::getInstance()->getOne2ManyAssociation($this->owner , 'Teams', new QueryObject)->toArray();
	}

	public function addTeam(ITeam $team)
	{
		AssociationFactory::getInstance()->getOne2ManyAssociation($this->owner , 'Teams', new QueryObject)->add($team);
	}

}