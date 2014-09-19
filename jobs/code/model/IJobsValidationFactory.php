<?php
/**
 * Interface IJobsValidationFactory
 */
interface IJobsValidationFactory {
	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForJobRegistration(array $data);
	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForJobRejection(array $data);
} 