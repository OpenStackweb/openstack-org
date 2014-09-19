<?php
/**
 * Class EventAlertEmail
 */
final class EventAlertEmail
	extends DataObject
	implements IEventAlertEmail
{

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
	);

	static $has_one = array(
		'LastEventRegistrationRequest' => 'EventRegistrationRequest',
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return IEventRegistrationRequest
	 */
	public function getLastEventRegistrationRequest()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'LastEventRegistrationRequest')->getTarget();
	}

	/**
	 * @param IEventRegistrationRequest $request
	 * @return void
	 */
	public function setLastEventRegistrationRequest(IEventRegistrationRequest $request)
	{
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'LastEventRegistrationRequest')->setTarget($request);
	}
}