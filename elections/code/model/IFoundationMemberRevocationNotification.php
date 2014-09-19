<?php

/**
 * Interface IFoundationMemberRevocationNotification
 */
interface IFoundationMemberRevocationNotification extends IEntity {

	const DaysBeforeRevocation = 30;
	/**
	 * @return string
	 */
	public function action();

	/**
	 * @return DateTime
	 */
	public function actionDate();

	/**
	 * @return string
	 */
	public function hash();

	/**
	 * @return DateTime
	 */
	public function sentDate();

	/**
	 * @return bool
	 */
	public function isExpired();

	/**
	 * @return bool
	 */
	public function isValid();

	/**
	 * @param IElection $latest_election
	 * @return void
	 */
	public function renew(IElection $latest_election);

	/**
	 * @return void
	 */
	public function revoke();

	/**
	 * @return void
	 */
	public function resign();

	/**
	 * @return IFoundationMember
	 */
	public function recipient();

	/**
	 * @return IElection
	 */
	public function latestElection();

	/**
	 * @return string
	 */
	public function generateHash();

	/**
	 * @return int
	 */
	public function remainingDays();

	/**
	 * @return DateTime
	 */
	public function expirationDate();

}