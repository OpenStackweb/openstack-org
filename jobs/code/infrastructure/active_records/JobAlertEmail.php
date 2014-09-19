<?php
/**
 * Class JobAlertEmail
 */
final class JobAlertEmail extends DataObject
	implements IJobAlertEmail {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	private static $db = array();

	static $has_one = array(
		'LastJobRegistrationRequest' => 'JobRegistrationRequest',
	);
	/**
	 * @return int
	 */
	public function getIdentifier(){
		return (int)$this->getField('ID');
	}
	/**
	 * @return IJobRegistrationRequest
	 */
	public function getLastJobRegistrationRequest()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'LastJobRegistrationRequest')->getTarget();
	}

	/**
	 * @param IJobRegistrationRequest $request
	 * @return void
	 */
	public function setLastJobRegistrationRequest(IJobRegistrationRequest $request)
	{
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'LastJobRegistrationRequest')->setTarget($request);
	}
}