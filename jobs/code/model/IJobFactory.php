<?php
/**
 * Interface IJobFactory
 */
interface IJobFactory {

	/**
	 * @param JobMainInfo       $info
	 * @param IJobLocation[]       $locations
	 * @param JobPointOfContact $point_of_contact
	 * @return IJobRegistrationRequest
	 */
	public function buildJobRegistrationRequest(JobMainInfo $info,
	                                            array $locations,
	                                            JobPointOfContact $point_of_contact);

	/**
	 * @param array $data
	 * @return JobMainInfo
	 */
	public function buildJobMainInfo(array $data);

	/**
	 * @param array $data
	 * @return IJobLocation[]
	 */
	public function buildJobLocations(array $data);

	/**
	 * @param array $data
	 * @return JobPointOfContact
	 */
	public function buildJobPointOfContact(array $data);

	/**
	 * @param IJobRegistrationRequest $request
	 * @return IJob
	 */
	public function buildJob(IJobRegistrationRequest $request);

	/**
	 * @param IJobRegistrationRequest $request
	 * @return IJobAlertEmail
	 */
	public function buildJobAlertEmail(IJobRegistrationRequest $request);
} 