<?php
/**
 * Interface ICLACompany
 */
interface ICLACompany extends IEntity {

	/**
	 * @return void
	 */
	public function signICLA();

	/**
	 * @return void
	 */
	public function unsignICLA();

	/**
	 * @return bool
	 */
	public function isICLASigned();

	/**
	 * @return DateTime
	 */
	public function ICLASignedDate();

	/**
	 * @return ITeam[]
	 */
	public function Teams();

	public function addTeam(ITeam $team);
} 