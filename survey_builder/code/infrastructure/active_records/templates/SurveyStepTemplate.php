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
        'Questions' => 'SurveyQuestionTemplate',
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
        $this->Name = str_replace(' ','-',strtolower($this->FriendlyName));
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
        if(empty($this->FriendlyName)){
            $valid->error('Friendly Name is empty!');
        }
        $slug = $this->getSlug();
        $id   = $this->ID;

        $res = DB::query("SELECT COUNT(ID) FROM SurveyStepTemplate WHERE Name = '{$slug}' AND ID <> {$id}")->value();
        if(intval($res) > 0 ){
            $valid->error('There is already another step with that name!');
        }
        return $valid;
    }
}