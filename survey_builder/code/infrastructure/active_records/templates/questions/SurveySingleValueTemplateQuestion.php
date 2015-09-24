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

class SurveySingleValueTemplateQuestion
    extends SurveyQuestionTemplate
    implements ISingleValueTemplateQuestion {

    static $db = array(
        'InitialValue' => 'Text',
    );

    private static $defaults = array(
        'InitialValue' => null,
    );

    static $many_many = array(
        'ValidationRules' => 'SurveySingleValueValidationRule'
    );

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->add(new TextField('InitialValue','Initial Value'));

        if($this->ID > 0 ){
            //validation rules
            $config = GridFieldConfig_RelationEditor::create();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $gridField = new GridField('ValidationRules', 'ValidationRules', $this->ValidationRules(), $config);
            $fields->add($gridField);
        }

        $fields->removeByName('ShowOnSangriaStatistics');
        $fields->removeByName('ShowOnPublicStatistics');
        return $fields;
    }

    /**
     * @return ISingleValueValidationRule[]
     */
    public function getValidationRules()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'ValidationRules')->toArray();
    }

    /**
     * @param ISingleValueValidationRule $validation_rule
     * @return void
     */
    public function addValidationRule(ISingleValueValidationRule $validation_rule)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'ValidationRules')->add($validation_rule);
    }

    /**
     * @return void
     */
    public function clearValidationRules()
    {
        //AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'ValidationRules');
    }

    /**
     * @return mixed
     */
    public function initialValue()
    {
        return $this->getField('InitialValue');
    }
}