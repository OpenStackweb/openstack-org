<?php
/**
 * Interface IAlertEmailRepository

 */
interface IAlertEmailRepository extends IEntityRepository {
	/**
	 * @return IJobAlertEmail
	 */
	public function getLastOne();
} 