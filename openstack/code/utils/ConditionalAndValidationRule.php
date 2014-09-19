<?php

class ConditionalAndValidationRule extends Validator {

    private $validators = array();

    public function __construct($validators=null){
        $this->validators = $validators;
    }

    function php($data) {
        $res = true;
        foreach($this->validators as $validator){
            $res &= $validator->php($data);
            $this->errors = array();
            if(!$res){
                $this->errors = array_merge($this->errors,$validator->getErrors());
                break;
            }
        }
        return $res;
    }



    function setForm($form) {
        $this->form = $form;
        foreach($this->validators as $validator){
            $validator->setForm($form);
        }
    }


    function javascript() {
        $js = "";
        foreach($this->validators as $validator){
            $js &= $validator->javascript();
        }
        return $js;
    }
}