<?php
/**
 * Interface IJobRegistrationRequest
 */
interface IJobRegistrationRequest extends IEntity {
	/**
	 * @param JobMainInfo $info
	 * @return void
	 */
	public function registerMainInfo(JobMainInfo $info);

	/**
	 * @param IJobLocation $location
	 * @return void
	 */
	public function registerLocation(IJobLocation $location);

	/**
	 * @param JobPointOfContact $point_of_contact
	 * @return void
	 */
	public function registerPointOfContact(JobPointOfContact $point_of_contact);

	/**
	 * @return void
	 */
	public function markAsPosted();

	/**
	 * @return void
	 */
	public function markAsRejected();
	/**
	 * @param Member $user
	 * @return void
	 */
	public function registerUser(Member $user);

	/**
	 * @return JobMainInfo
	 */
	function getMainInfo();

	/**
	 * @return JobPointOfContact
	 */
	function getPointOfContact();

	/**
	 * @return IJobLocation[]
	 */
	public function getLocations();

	/**
	 * @return void
	 */
	public function clearLocations();
}