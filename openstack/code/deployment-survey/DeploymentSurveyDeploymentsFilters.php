<?php

class DeploymentSurveyDeploymentsFilters extends Form {
 
    function __construct($controller, $name, $action, $start_date, $end_date) {

        // Define fields //////////////////////////////////////

        // esto lo invente para probar, meto el action (osea el controller que lo llama en un hidden
        $callerAction = new HiddenField('caller-action','caller-action',$action);
        $StartDateTime = new TextField('date-from','Start Date');
        $StartDateTime->addExtraClass('date inline');
        $StartDateTime->setValue($start_date);
        $EndDateTime = new TextField('date-to','End Date');
        $EndDateTime->addExtraClass('date inline');
        $EndDateTime->setValue($end_date);

        $fields = new FieldSet (
            $callerAction,$StartDateTime,$EndDateTime
        );

        $submit_filters = new FormAction('FilterResults', 'Go !');
        $submit_filters->addExtraClass('submit_filters');
        $actions = new FieldSet(
            $submit_filters
        );

        parent::__construct($controller, $name, $fields, $actions);

    }
 
    function forTemplate() {
        return $this->renderWith(array(
            $this->class,
            'Form'
        ));
    }


  
}