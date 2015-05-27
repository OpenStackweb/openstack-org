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

class SurveyQuestionTemplate
    extends DataObject
    implements ISurveyQuestionTemplate {

    static $db = array(
        'Name'         => 'VarChar(255)',
        'Label'        => 'Text',
        'Order'        => 'Int',
        'Mandatory'    => 'Boolean',
        'ReadOnly'     => 'Boolean',
    );

    static $has_one = array(
        'Step' => 'SurveyStepTemplate',
    );

    static $indexes = array(
        'StepID_Name' => array('type' => 'unique', 'value' => 'StepID,Name')
    );

    static $belongs_to = array(

    );

    static $many_many = array(
    );

    static $has_many = array(
    );

    private static $defaults = array(
        'Mandatory' => true,
        'ReadOnly'  => false,
    );


    private static $summary_fields = array(
        'Type',
        'Name',
    );

    public function Type(){
        return '';
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator_fields  = new RequiredFields(array('Name','Label'));

        return $validator_fields;
    }


    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return string
     */
    public function label()
    {
        return $this->getField('Label');
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->getField('Name');
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
    public function isMandatory()
    {
        return $this->getField('Mandatory');
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->getField('ReadOnly');
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * @return ISurveyDependantQuestionTemplate[]
     */
    public function dependsOn()
    {
        // TODO: Implement dependsOn() method.
    }
}