<?php

/**
 * Class Vote
 */
final class Vote
	extends DataObject
	implements IVote {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(

	);

	static $has_one = array(
		'Voter'    => 'Member',
		'Election' => 'Election',
	);

	static $indexes = array(
		'Voter_Election' => array('type'=>'unique', 'value'=>'VoterID,ElectionID'),
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return IFoundationMember
	 */
	public function voter()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Voter')->getTarget();
	}

	/**
	 * @return IElection
	 */
	public function election()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Election')->getTarget();
	}

	/**
	 * @return DateTime
	 */
	public function date()
	{
		// TODO: Implement date() method.
	}
}