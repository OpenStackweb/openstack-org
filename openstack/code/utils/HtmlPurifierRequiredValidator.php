<?php


class HtmlPurifierRequiredValidator  extends Validator {


    protected $required;

    /**
     * Pass each field to be validated as a seperate argument
     * to the constructor of this object. (an array of elements are ok)
     */
    function __construct() {
        $Required = func_get_args();
        if( isset($Required[0]) && is_array( $Required[0] ) )
            $Required = $Required[0];
        $this->required = $Required;

        parent::__construct();
    }

    function javascript()
    {
        return '';
    }

    function php($data)
    {
        $valid = true;


        $fields = $this->form->Fields();

        if($this->required) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('CSS.AllowedProperties', array());
            $purifier = new HTMLPurifier($config);

            foreach($this->required as $fieldName) {
                $formField = $fields->dataFieldByName($fieldName);

                // submitted data for file upload fields come back as an array
                $value = isset($data[$fieldName]) ? $data[$fieldName] : null;

                if(is_array($value)) {
                    if ($formField instanceof FileField && isset($value['error']) && $value['error']) {
                        $error = true;
                    }
                    else {
                        $error = (count($value)) ? false : true;
                    }
                } else {
                    // assume a string or integer
                    $error = (strlen($value)) ? false : true;
                }

                if($formField && $error) {
                    $errorMessage = sprintf( '%s is not valid'.'.', strip_tags('"' . ($formField->Title() ? $formField->Title() : $fieldName) . '"'));
                    if($msg = $formField->getCustomValidationMessage()) {
                        $errorMessage = $msg;
                    }
                    $this->validationError(
                        $fieldName,
                        $errorMessage,
                        "required"
                    );
                    $valid = false;
                }
                else{
                    $cleaned_value = $purifier->purify($value);
                    if(is_null($cleaned_value) || empty($cleaned_value))
                    {
                        $errorMessage = sprintf( '%s is invalid'.'.', strip_tags('"' . ($formField->Title() ? $formField->Title() : $fieldName) . '"'));
                        $this->validationError(
                            $fieldName,
                            $errorMessage,
                            "required"
                        );
                        $valid = false;
                    }
                }

            }
        }
        return $valid;
    }
}