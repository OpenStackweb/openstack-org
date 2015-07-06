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
            $count = $this->Steps()->filter('ClassName', 'SurveyThankYouStepTemplate')->count();
            if(intval($count) === 0){
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
        // fix for order
    }

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        foreach($this->Steps() as $s){
            $s->delete();
        }
        foreach($this->EntitySurveys() as $e){
            $e->delete();
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
        $query = new QueryObject();
        $query->addOrder(QueryOrder::asc('Order'));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Steps', $query)->toArray();
    }

    /**
     * @param ISurveyStepTemplate $step
     * @return void
     */
    public function addStep(ISurveyStepTemplate $step)
    {
        $query = new QueryObject();
        $query->addOrder(QueryOrder::asc('Order'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Steps', $query)->add($step);
    }

    /**
     * @return ISurveyStepTemplate
     */
    public function getDefaultStep()
    {
        return $this->Steps()->sort('Order','ASC')->first();
    }

    /**
     * @param string $step
     * @return ISurveyStepTemplate
     */
    public function getStepBySlug($step){
        return $this->Steps()->filter('Name', $step)->first();
    }

    /**
     * @return ISurveyStepTemplate
     */
    public function getLastStep()
    {
        return $this->Steps()->sort('Order','ASC')->last();
    }

    protected function validate() {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        if(empty($this->Title)){
            return $valid->error('Friendly Name is empty!');
        }
        $title = $this->Title;
        $id    = $this->ID;

        $res = DB::query("SELECT COUNT(ID) FROM SurveyTemplate WHERE Title = '{$title}' AND ClassName = 'SurveyTemplate' AND ID <> {$id}")->value();
        if(intval($res) > 0 ){
            return $valid->error('There is already another survey template with that name!');
        }

        //check dates ranges

        $start_date = new \DateTime($this->StartDate, new DateTimeZone('UTC'));
        $end_date = new \DateTime($this->EndDate, new DateTimeZone('UTC'));

        if($start_date >= $end_date){
            return $valid->error('selected date range is invalid!');
        }

        $start_date = $this->StartDate;
        $end_date   = $this->EndDate;

        $res = DB::query("SELECT COUNT(ID) FROM SurveyTemplate WHERE ClassName = 'SurveyTemplate' AND ID <> {$id} AND ( (StartDate <= '{$end_date}')  AND  (EndDate >= '{$start_date}')) ;")->value();
        if(intval($res) > 0 ){
            return $valid->error('There is already another valid survey template under that date range!');
        }
        return $valid;
    }
}