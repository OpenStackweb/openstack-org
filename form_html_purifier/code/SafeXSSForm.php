<?php

class SafeXSSForm extends Form {

	private static $allowed_actions = array('httpSubmission');

	function __construct($controller, $name, FieldList $fields, FieldList $actions, $validator = null) {
		parent::__construct($controller, $name, $fields, $actions, $validator);
	}
    /**
     * Save the contents of this form into the given data object.
     * It will make use of setCastedField() to do this.
     *
     * @param $dataObject The object to save data into
     * @param $fieldList An optional list of fields to process.  This can be useful when you have a
     * form that has some fields that save to one object, and some that save to another.
     */
    function saveInto(DataObjectInterface $dataObject, $fieldList = null) {
        $dataFields = $this->fields->saveableFields();
        $lastField = null;

        $config = HTMLPurifier_Config::createDefault();
        // Remove any CSS or inline styles
        $config->set('CSS.AllowedProperties', array());
        $purifier = new HTMLPurifier($config);

        if($dataFields) foreach($dataFields as $field) {
            // Skip fields that have been excluded
            if($fieldList && is_array($fieldList) && !in_array($field->Name(), $fieldList)) continue;
            $saveMethod = "save{$field->getName()}";
            //purify
            $value = $field->dataValue();
            $class = get_class($field);
            if(is_string($value) && ($class=='TextField' || $class=="TextareaField"))
                $field->setValue($purifier->purify($value));

            if($field->getName() == "ClassName"){
                $lastField = $field;
            }else if( $dataObject->hasMethod( $saveMethod ) ){
                $dataObject->$saveMethod( $field->dataValue());
            } else if($field->getName() != "ID"){
                $field->saveInto($dataObject);
            }
        }
        if($lastField) $lastField->saveInto($dataObject);
    }

	function httpSubmission($request) {
		// Protection against CSRF attacks
		$token = $this->getSecurityToken();
		if(!$token->checkRequest($request)) {
			$this->httpError(412, "Security token doesn't match, possible CSRF attack.");
		}

		return parent::httpSubmission($request);
	}

	public function httpError($code, $message = null) {
		if (!(Permission::check("ADMIN"))) {
			$response = ErrorPage::response_for($code);
		}
		if (empty($response)) $response = $message;
		throw new SS_HTTPResponse_Exception($response);
	}

}