<?php
/**
 * Interface IEventValidatorFactory
 */
interface IEventValidatorFactory {
	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForEventRegistration(array $data);
	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForEventRejection(array $data);
} 