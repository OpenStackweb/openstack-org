<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 10/2/13
 * Time: 4:28 PM
 * To change this template use File | Settings | File Templates.
 */

class AffiliationEditForm extends Form{

    function __construct($controller, $name) {

        $fields = new FieldList (
            new TextField("OrgName","Organization"),
            new TextField('StartDate','Start Date'),
            new TextField('EndDate','End Date'),
            new CheckboxField('Current','Is Current?')
        );

        $fields->push(new HiddenField("Id","Id","0"));
        $actions = new FieldList();
        parent::__construct($controller, $name, $fields, $actions);
    }

    function forTemplate() {
        return $this->renderWith(array(
            $this->class,
            'Form'
        ));
    }
}