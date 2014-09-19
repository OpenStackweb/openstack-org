<?php
/**
 * Interface IEventAlertEmail
 */
interface IEventAlertEmail extends IEntity{
	/**
	 * @return IEventRegistrationRequest
	 */
	public function getLastEventRegistrationRequest();

	/**
	 * @param IEventRegistrationRequest $request
	 * @return void
	 */
	public function setLastEventRegistrationRequest(IEventRegistrationRequest $request);
} 