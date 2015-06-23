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

class SurveyStepTemplate
    extends DataObject
    implements ISurveyStepTemplate
{
    static $db = array(
        'Name'         => 'VarChar(255)',
        'Content'      => 'HTMLText',
        'FriendlyName' => 'Text',
        'Order'        => 'Int',
        'SkipStep'     => 'Boolean',
    );

    static $indexes = array(
        'SurveyTemplateID_Name' => array('type' => 'unique', 'value' => 'SurveyTemplateID,Name')
    );

    static $has_one = array(
        'SurveyTemplate' => 'SurveyTemplate'
    );

    static $belongs_to = array(
        'SkipStep' => false,
    );

    static $many_many = array(
    );

    static $has_many = array(

    );

    private static $defaults = array(
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return ISurveyTemplate;
     */
    public function survey()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'SurveyTemplate')->getTarget();
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->getField('Name');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getField('Name');
    }

    /**
     * @return string
     */
    public function friendlyName()
    {
        return $this->getField('FriendlyName');
    }

    /**
     * @return string
     */
    public function content()
    {
        return $this->getField('Content');
    }

    /**
     * @return int
     */
    public function order()
    {
        return (int)$this->getField('Order');
    }

    /**
     * @return bool
     */
    public function canSkip()
    {
        return $this->getField('SkipStep');
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->getSlug();
    }


    protected function getSlug(){
        // normalize to be url friendly
        $this->Name = UrlUtils::getSlug($this->FriendlyName);
        return $this->Name;
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator_fields  = new RequiredFields(array('FriendlyName'));
        return $validator_fields;
    }

    protected function validate() {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        if(empty($this->FriendlyName)){
            return $valid->error('Friendly Name is empty!');
        }
        $slug     = $this->getSlug();
        $id       = $this->ID;
        $owner_id = $this->SurveyTemplateID;

        $res = DB::query("SELECT COUNT(ID) FROM SurveyStepTemplate WHERE Name = '{$slug}' AND ID <> {$id} AND SurveyTemplateID = {$owner_id};")->value();
        if(intval($res) > 0 ){
            return $valid->error('There is already another step with that name!');
        }
        return $valid;
    }

    protected function fixOrder(){

        $id = $this->ID;
        // fix for thank u step
        $last_step  = $this->survey()->getLastStep();
        $new_order = $order = count($this->survey()->getSteps());
        $last_id   = $last_step->getIdentifier();
        if($last_step instanceof ISurveyThankYouStepTemplate){
            $order = $order - 1;
            DB::query(" UPDATE SurveyStepTemplate SET `Order` = {$new_order} WHERE ID = {$last_id} ");
        }

        DB::query(" UPDATE SurveyStepTemplate SET `Order` = {$order} WHERE ID = {$id} ");

    }

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
    }
}