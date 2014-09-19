<?php
/**
 * Interface IEventAlertEmailRepository
 */
interface IEventAlertEmailRepository extends IEntityRepository {
	/**
	 * @return IEventAlertEmail
	 */
	public function getLastOne();
} 