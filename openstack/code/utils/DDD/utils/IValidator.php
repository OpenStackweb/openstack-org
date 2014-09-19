<?php

/**
 * Interface IValidator
 */
interface IValidator {

	/**
	 * @return bool
	 */
	public function fails();

	/**
	 * @return bool
	 */
	public function passes();

	/**
	 * @return array
	 */
	public function messages();
} 