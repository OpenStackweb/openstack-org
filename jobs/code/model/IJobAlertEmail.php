<?php
interface IJobAlertEmail extends IEntity {
	/**
	 * @return IJobRegistrationRequest
	 */
	public function getLastJobRegistrationRequest();
	/**
	 * @param IJobRegistrationRequest $request
	 * @return void
	 */
	public function setLastJobRegistrationRequest(IJobRegistrationRequest $request);
} 