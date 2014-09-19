<?php
/**
 * Class SapphireEventAlertEmailRepository
 */
final class SapphireEventAlertEmailRepository
	extends SapphireRepository
	implements IEventAlertEmailRepository {

	public function __construct(){
		parent::__construct(new EventAlertEmail);
	}

	/**
	 * @return IEventAlertEmail
	 */
	public function getLastOne() {
		$query = new QueryObject(new EventAlertEmail);
		$query->addOrder(QueryOrder::desc('Created'));
		return $this->getBy($query);
	}
}