<?php

/**
 * Class CCLAValidatorFactory
 */
final class CCLAValidatorFactory implements ICCLAValidatorFactory {

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForTeamInvitation(array $data)
	{
		$rules = array(
			'first_name' => 'required|text',
			'last_name'  => 'required|text',
			'email'      => 'required|email',
			'team_id'    => 'required|integer',
			'member_id'  => 'sometimes|integer',
		);

		$messages = array(
			'first_name.required'        => ':attribute is required',
			'first_name.text'            => ':attribute should be valid text.',
			'last_name.required'         => ':attribute is required',
			'email.email'                => ':attribute should be valid email.',
			'email.required'             => ':attribute is required',
			'team_id.required'           => ':attribute is required',
			'team_id.integer      '      => ':attribute should be valid integer.',
			'member_id.integer'          => ':attribute should be valid integer.',
		);

		return ValidatorService::make($data, $rules, $messages);
	}

	/**
	 * @param array $data
	 * @return ValidatorService
	 */
	public function buildValidatorForTeam(array $data){
		$rules = array(
			'name'       => 'required|text',
			'company_id' => 'required|integer',
		);

		$messages = array(
			'name.required'        => ':attribute is required',
			'name.text'            => ':attribute should be valid text.',
			'company_id.required'  => ':attribute is required',
			'company_id.integer'   => ':attribute should be valid integer.',
		);

		return ValidatorService::make($data, $rules, $messages);
	}
}