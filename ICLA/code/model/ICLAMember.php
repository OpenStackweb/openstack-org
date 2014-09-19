<?php

/**
 * Interface ICLAMember
 */
interface ICLAMember extends IEntity {

	const CCLAGroupSlug      = 'ccla-admin';
	const CCLAPermissionSlug = 'CCLA_ADMIN';

	/**
	 * @return string
	 */
	public function getGerritId();

	/**
	 * @return DateTime
	 */
	public function getLastCommitedDate();


	/**
	 * @param int $gerrit_id
	 * @return void
	 */
	public function signICLA($gerrit_id);

	/**
	 * @param DateTime $date
	 * @return void
	 */
	public function updateLastCommitedDate(DateTime $date);

	/**
	 * @return bool
	 */
	public function isCCLAAdmin();

	/**
	 * @return ICLACompany
	 */
	public function getManagedCCLACompany();

	/**
	 * @return bool
	 */
	public function hasSignedCLA();
}