<?php

class SummitForm extends BootstrapForm
{

    public function __construct($summit,$controller, $name, $actions) {
        parent::__construct(
            $controller, 
            $name, 
            $this->getSummitFields($summit),
            $actions
        );

        $this->setTemplate($this->class);

        $this->customise(
            array(
                'Summit' => $summit
            )
        );

    }


    protected function getSummitFields(ISummit $summit) {
        $fields = FieldList::create(
            TextField::create('Name')->setAttribute('autofocus','TRUE'),
            TextField::create('SummitBeginDate'),
            TextField::create('SummitEndDate'),
            DropdownField::create('EventTypes','',$summit->getEventTypes()->map("Type"))->setAttribute('data-role','tagsinput')->setAttribute('multiple','multiple')
        );

        return $fields;
    }


}