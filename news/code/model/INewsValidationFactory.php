<?php
/**
 * Interface INewsValidationFactory
 */
interface INewsValidationFactory {
	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForNewsRegistration(array $data);
	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForNewsRejection(array $data);
} 