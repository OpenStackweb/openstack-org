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
class AbstractSurveyMigrationMapping extends DataObject implements IMigrationMapping
{
    static $db = array
    (

    );

    static $indexes = array(

    );

    static $has_one = array
    (
        'TargetField'  => 'SurveyQuestionTemplate',
        'TargetSurvey' => 'SurveyTemplate'
    );

    static $belongs_to = array(

    );

    static $many_many = array(
    );

    static $has_many = array(
    );

    private static $defaults = array(
    );

     /**
     * @return ISurveyTemplate
     */
    public function getTargetSurvey()
    {
       return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'TargetSurvey')->getTarget();
    }

    /**
     * @return ISurveyQuestionTemplate
     */
    public function getTargetQuestion()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'TargetField')->getTarget();
    }

    public function getCMSFields()
    {
        $field = parent::getCMSFields();

        $field->addFieldToTab('Root.Main',  new HiddenField('TargetSurveyID', 'TargetSurveyID'));

        $survey_id = isset($_REQUEST['survey_template_id'])?intval($_REQUEST['survey_template_id']) : $this->TargetSurveyID;

        $template  = SurveyTemplate::get()->filter('ID', $survey_id)->first();
        $steps     = $template->Steps()->filter('ClassName','SurveyRegularStepTemplate');
        $questions = array();

        foreach($steps as $step)
        {
            foreach($step->getQuestions() as $question)
            {
                if($question instanceof SurveyLiteralContentQuestionTemplate) continue;
                $questions[$question->ID] = $step->Name.' -> '. $question->Name;
            }
        }

        $field->addFieldToTab('Root.Main',  $ddl = new DropdownField('TargetFieldID', 'Target Field', $questions));

        $ddl->setEmptyString('-- select target field --');

        return $field;
    }

    public function getCMSValidator() {
        return new RequiredFields(array(
            'TargetFieldID'
        ));
    }

    public function validate() {
        $result = parent::validate();

        if(intval($this->TargetFieldID) <= 0) {
            $result->error('you need to specify a target field!');
        }

        return $result;
    }
    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }
}