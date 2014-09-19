<?php
/**
 * Class SapphireJobAlertEmailRepository
 */
final class SapphireJobAlertEmailRepository
	extends SapphireRepository
	implements IAlertEmailRepository
{

	public function __construct(){
		parent::__construct(new JobAlertEmail);
	}
	/**
	 * @return IJobAlertEmail
	 */
	public function getLastOne() {
		$query = new QueryObject(new EventAlertEmail);
		$query->addOrder(QueryOrder::desc('Created'));
		return $this->getBy($query);
	}
} 