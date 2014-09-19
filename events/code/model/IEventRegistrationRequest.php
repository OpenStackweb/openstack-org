<?php
/**
 * Interface IEventRegistrationRequest
 */
interface IEventRegistrationRequest extends IEntity {
	/**
	 * @param EventPointOfContact $point_of_contact
	 * @return void
	 */
	function registerPointOfContact(EventPointOfContact $point_of_contact);

	/**
	 * @return EventPointOfContact
	 */
	function getPointOfContact();

	/**
	 * @param EventMainInfo $info
	 * @return void
	 */
	function registerMainInfo(EventMainInfo $info);

	/**
	 * @return EventMainInfo
	 */
	function getMainInfo();

	/**
	 * @param EventLocation $location
	 * @return void
	 */
	public function registerLocation(EventLocation $location);

	/**
	 * @return EventLocation
	 */
	public function getLocation();
	/**
	 * @param EventDuration $duration
	 * @return void
	 */
	public function registerDuration(EventDuration $duration);

	/**
	 * @return EventDuration
	 */
	public function getDuration();

	/**
	 * @param Member $user
	 * @return void
	 */
	public function registerUser(Member $user);

	/**
	 * @return boolean
	 */
	public function hasRegisteredUser();

	/**
	 * @return Member
	 */
	public function getRegisteredUser();


	/**
	 * @param SponsorInfo $sponsor_info
	 * @return void
	 */
	public function registerSponsor(SponsorInfo $sponsor_info);

	/**
	 * @throws EntityValidationException
	 */
	public function markAsRejected();

	/**
	 * @throws EntityValidationException
	 */
	public function markAsPosted();
}