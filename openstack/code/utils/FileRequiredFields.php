<?php

class FileRequiredFields extends RequiredFields {

    protected $required_files_fields;

    public function setRequiredFileFields($required_files_fields){
        $this->required_files_fields = $required_files_fields;
    }

    function php($data) {
        $valid = parent::php($data);
        // if we are in a complex table field popup, use ctf[childID], else use ID
        if(isset($_REQUEST['ctf']['childID'])) {
            $id = $_REQUEST['ctf']['childID'];
        } else {
            $id =  $this->form->record->ID;
        }

        if(isset($id)){
            if(isset($_REQUEST['ctf']['ClassName'])) {
                $class = $_REQUEST['ctf']['ClassName'];
            } else {
                $class = $this->form->record->class;
            }
            $object = $class::get()->byId($id);
            if($object){
                foreach($this->required_files_fields as $key => $value) {
                    $key = is_string($key)?$key:$value;
                    $fileId = $object->{$key}()->ID;
                    if(!$fileId){
                        $name = isset($value) && is_array($value)  && array_key_exists('Name',$value)?$value['Name']:$key;
                        $errorMessage = sprintf(_t('Form.FIELDISREQUIRED', '%s is required').'.', strip_tags('"' . ($name) . '"'));
                        $this->validationError($key,$errorMessage,"required");
                        $valid = false;
                        break;
                    }
                }
            }
        }
        return $valid;
    }
}