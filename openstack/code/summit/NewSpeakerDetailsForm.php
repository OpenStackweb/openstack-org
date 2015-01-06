<?php

class NewSpeakerDetailsForm extends Form {
    public function __construct($controller, $name) {
        $fields = new FieldList(
            TextField::create("FirstName"),
            TextField::create("Surname")
        );
        $actions = new FieldList(FormAction::create("doSave")->setTitle("Save"));
        
        // Create validators
        $validator = RequiredFields::create(array("FirstName","Surname")); 

        parent::__construct($controller, $name, $fields, $actions, $validator);        
        
    }
    public function doSave(array $data, Form $form) {
        // Do something with $data
        Controller::curr()->redirectBack();
    }
    public function forTemplate() {
        return $this->renderWith(array($this->class, 'Form'));
    }
}