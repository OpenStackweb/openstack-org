<?php
/**
 * Class NewsValidationFactory
 */
final class NewsValidationFactory
	implements INewsValidationFactory{

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForNewsRegistration(array $data){

		$rules = array(
			'headline'        => 'required|text|max:100',
			'datetime'        => 'required|datetime',
			'summary'         => 'required|htmltext',
			'tags'            => 'required|text',
			'embargo_date'    => 'required|datetime',
			'submitter_name'  => 'required|text',
			'submitter_email' => 'required|email',
		);

		$messages = array(
			'headline.required'        => ':attribute is required',
			'headline.text'            => ':attribute should be valid text.',
			'headline.max'             => ':attribute should have less than 100 chars.',
			'datetime.required'        => ':attribute is required',
			'datetime.datetime'        => ':attribute should be a valid date.',
			'submitter_name.required'  => ':attribute is required',
			'submitter_name.text'      => ':attribute should be valid text.',
			'submitter_email.required' => ':attribute is required',
			'submitter_email.email'    => ':attribute should be valid email.',
			'summary.required'         => ':attribute is required',
			'summary.htmltext'         => ':attribute should be valid text.',
			'tags.required'            => ':attribute is required',
			'tags.text'                => ':attribute should be valid text.',
			'embargo_date.required'    => ':attribute is required',
			'embargo_date.datetime'    => ':attribute should be a valid date.',
		);

		return ValidatorService::make($data, $rules, $messages);
	}

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForNewsRejection(array $data){
		$rules = array(
			'custom_reject_message' => 'sometimes|text'
		);

		$messages = array(
			'custom_reject_message.text' => ':attribute should be valid text.'
		);

		return ValidatorService::make($data, $rules, $messages);
	}
}