<?php
/**
 * Interface INewsValidationFactory
 */
interface INewsValidationFactory {
	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForNews(array $data);
	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForNewsRejection(array $data);
} 