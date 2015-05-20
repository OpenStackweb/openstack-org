<?php
/**
 * Copyright 2015 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

class SurveyTemplate
    extends DataObject
    implements ISurveyTemplate {

    static $db = array(
        'Title'     => 'VarChar(255)',
        'StartDate' => 'SS_Datetime',
        'EndDate'   => 'SS_Datetime',
        'Enabled'   => 'Boolean',
    );

    static $indexes = array(
        'Title' => array('type' => 'unique', 'value' => 'Title')
    );

    static $has_one = array(
        'CreatedBy' => 'Member',
    );

    static $many_many = array(
    );

    static $has_many = array(
        'Steps'         => 'SurveyStepTemplate',
        'EntitySurveys' => 'EntitySurveyTemplate',
    );

    private static $defaults = array(
    );

    public function getCMSFields() {

        $fields = new FieldList();

        $fields->add(new TextField('Title','Title'));

        $start_date = new DatetimeField('StartDate', 'Start Date');
        $end_date   = new DatetimeField('EndDate', 'End Date');

        $start_date->getDateField()->setConfig('showcalendar', true);
        $start_date->setConfig('dateformat', 'dd/MM/yyyy');

        $end_date->getDateField()->setConfig('showcalendar', true);
        $end_date->setConfig('dateformat', 'dd/MM/yyyy');

        $fields->add($start_date);
        $fields->add($end_date);
        $fields->add(new CheckboxField('Enabled','Is Enabled'));
        $fields->add(new HiddenField('CreatedByID','CreatedByID', Member::currentUserID()));

        //steps
        if($this->ID > 0) {
            // steps
            $config = GridFieldConfig_RecordEditor::create();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();

            $step_types = array(
                'SurveyRegularStepTemplate'       => 'Regular Step' ,
                'SurveyDynamicEntityStepTemplate' => 'Entities Holder Step',
            );

            if(intval($this->Steps()->filter('Name', 'thankyou')->count()) === 0){
                $step_types['SurveyThankYouStepTemplate'] ='Thank You (Final)';
            }

            $multi_class_selector->setClasses(
                $step_types
            );

            $config->addComponent($multi_class_selector);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $gridField = new GridField('Steps', 'Steps', $this->Steps(), $config);
            $fields->add( $gridField);

            //entities
            $config    = GridFieldConfig_RecordEditor::create();
            $gridField = new GridField('EntitySurveys', 'Entities', $this->EntitySurveys(), $config);
            $fields->add($gridField);
        }
        return $fields;
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator_fields  = new RequiredFields(array('Title','StartDate', 'EndDate'));

        return $validator_fields;
    }

    protected function onAfterWrite() {
        parent::onAfterWrite();
        if($this->Steps()->filter('Name', 'thankyou')->count() > 0) {
            $order = count($this->getSteps()) + 1;
            $id    = $this->Steps()->filter('Name', 'thankyou')->first()->ID;
            DB::query(" UPDATE SurveyStepTemplate SET `Order` = {$order} WHERE ID = {$id} ");
        }
    }


    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getField('Enabled');
    }

    /**
     * @return boolean
     */
    public function isVoid()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        $start_date = new \DateTime($this->StartDate, new DateTimeZone('UTC'));
        $end_date = new \DateTime($this->EndDate, new DateTimeZone('UTC'));
        return $now >= $start_date && $now <= $end_date;
    }

    /**
     * @return IFoundationMember
     */
    public function owner()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'CreatedBy')->getTarget();
    }

    /**
     * @return ISurveyStepTemplate[]
     */
    public function getSteps()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Steps')->toArray();
    }

    /**
     * @param ISurveyStepTemplate $step
     * @return void
     */
    public function addStep(ISurveyStepTemplate $step)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Steps')->add($step);
    }

    /**
     * @return ISurveyStepTemplate
     */
    public function getDefaultStep()
    {
        return $this->Steps()->first();
    }

    /**
     * @param string $step
     * @return ISurveyStepTemplate
     */
    public function getStepBySlug($step){
        return $this->Steps()->filter('Name', $step)->first();
    }
}