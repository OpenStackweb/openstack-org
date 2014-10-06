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

        $submit_filters = new FormAction('filterResults', 'Go !');
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

    public static function filterResults($data, $form) {
        //recupero el action que estaba en el hidden, igual aca ni frena el debug...
        $action = $data['caller-action'];
        $date_from = $data['date-from'];
        $date_to = $data['date-to'];
        // redirecciono al controller que lo llama.. no funca
        return Controller::curr()->redirect($action."/".$date_from."/".$date_to);

    }
  
}