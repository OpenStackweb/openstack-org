<?php
/**
 * Class JobsValidationFactory
 */
final class JobsValidationFactory
	implements IJobsValidationFactory{

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForJobRegistration(array $data){

		$rules = array(
			'title'                  => 'required|text|max:100',
			'url'                    => 'required|url',
			'description'            => 'required|htmltext',
			'instructions'           => 'required|htmltext',
			'company_name'           => 'required|text',
			'point_of_contact_name'  => 'required|text',
			'point_of_contact_email' => 'required|email',
		);

		$messages = array(
			'title.required'                  => ':attribute is required',
			'title.text'                      => ':attribute should be valid text.',
			'title.max'                       => ':attribute should have less than 100 chars.',
			'url.required'                    => ':attribute is required',
			'url.url'                         => ':attribute should be valid url.',
			'point_of_contact_name.required'  => ':attribute is required',
			'point_of_contact_name.text'      => ':attribute should be valid text.',
			'point_of_contact_email.required' => ':attribute is required',
			'point_of_contact_email.email'    => ':attribute should be valid email.',
			'description.required'            => ':attribute is required',
			'description.text'                => ':attribute should be valid text.',
			'instructions.required'           => ':attribute is required',
			'instructions.htmltext'           => ':attribute should be valid text.',
			'company_name.required'           => ':attribute is required',
			'company_name.htmltext'           => ':attribute should be valid text.',
		);

		return ValidatorService::make($data, $rules, $messages);
	}

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForJobRejection(array $data){
		$rules = array(
			'send_rejection_email' => 'required|boolean',
			'custom_reject_message' => 'sometimes|text'
		);

		$messages = array(
			'send_rejection_email.required' => ':attribute is required',
			'send_rejection_email.boolean' => ':attribute should be valid boolean.',
			'custom_reject_message.text' => ':attribute should be valid text.'
		);

		return ValidatorService::make($data, $rules, $messages);
	}
}