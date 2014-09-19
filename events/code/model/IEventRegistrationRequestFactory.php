<?php

interface IEventRegistrationRequestFactory {
	/**
	 * @param EventMainInfo $info
	 * @param EventPointOfContact $point_of_contact
	 * @param EventLocation $location
	 * @param EventDuration $duration
	 * @param SponsorInfo   $sponsor
	 * @return IEventRegistrationRequest
	 */
	public function buildEventRegistrationRequest(EventMainInfo $info,
	                                              EventPointOfContact $point_of_contact,
	                                              EventLocation $location,
	                                              EventDuration $duration,
	                                              SponsorInfo $sponsor);

	/**
	 * @param array $data
	 * @return EventMainInfo
	 */
	public function buildEventMainInfo(array $data);

	/**
	 * @param array $data
	 * @return EventLocation
	 */
	public function buildEventLocation(array $data);

	/**
	 * @param array $data
	 * @return EventDuration
	 */
	public function buildEventDuration(array $data);

	/**
	 * @param array $data
	 * @return SponsorInfo
	 */
	public function buildSponsorInfo(array $data);

	/**
	 * @param array $data
	 * @return EventPointOfContact
	 */
	public function buildPointOfContact(array $data);

	public function buildEvent(IEventRegistrationRequest $request);

	public function buildEventAlertEmail(IEventRegistrationRequest $last);
} 