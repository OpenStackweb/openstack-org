<?php

/**
 * Interface ICCLAValidatorFactory
 */
interface ICCLAValidatorFactory {
	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForTeamInvitation(array $data);

	/**
	 * @param array $data
	 * @return ValidatorService
	 */
	public function buildValidatorForTeam(array $data);
} 