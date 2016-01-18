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
class NewDataModelSurveyMigrationMapping extends AbstractSurveyMigrationMapping implements INewSurveyMigrationMapping
{
    static $db = array
    (

    );

    static $indexes = array(

    );

    static $has_one = array
    (
        'OriginField'  => 'SurveyQuestionTemplate',
        'OriginSurvey' => 'SurveyTemplate'
    );

    static $belongs_to = array
    (
    );

    static $many_many = array(
    );

    static $has_many = array(
    );

    private static $defaults = array(
    );

    private static $summary_fields = array(
    );

    public static function getDisplayFields()
    {
        return array
        (
            'OriginSurvey.Title' => 'Origin Survey',
            'OriginField.Name' => 'Origin Field',
            'TargetField.Name' => 'Target Field',
        );
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $survey_id = isset($_REQUEST['survey_template_id'])?intval($_REQUEST['survey_template_id']) : $this->TargetSurveyID;

        $current_template = SurveyTemplate::get()->byID($survey_id);

        $templates = SurveyTemplate::get()->exclude('ID', $survey_id);
        $allowed_templates = new ArrayList();
        foreach($templates as $template) {
            if($template->ClassName !== $current_template->ClassName) continue;
            $allowed_templates->add($template);
        }

        $fields->addFieldToTab('Root.Main',  $ddl_template = new DropdownField('OriginSurveyID', 'Origin Survey', $allowed_templates->map('ID', 'Title')));
        $ddl_template->setEmptyString('-- select a survey template --');
        $fields->addFieldToTab('Root.Main',  $ddl_fields = new DropdownField('OriginFieldID'  , 'Origin Field'));
        if(intval($this->OriginFieldID) > 0)
        {
            $ddl_fields->setAttribute('data-value', $this->OriginFieldID);
        }
        return $fields;
    }

    public function getCMSValidator() {
        $validator = parent::getCMSValidator();
        $validator->addRequiredField('OriginSurveyID');
        $validator->addRequiredField('OriginFieldID');
        return $validator;
    }

    public function validate() {
        $result = parent::validate();

        if(intval($this->OriginSurveyID) <= 0) {
            $result->error('you need to specify a origin survey!');
        }

        if(intval($this->OriginFieldID) <= 0) {
            $result->error('you need to specify a origin field!');
        }


        $survey_id = isset($_REQUEST['survey_template_id'])?intval($_REQUEST['survey_template_id']) : $this->TargetSurveyID;

        $current_template = SurveyTemplate::get()->byID($survey_id);

        $old_mapping      = $current_template->MigrationMappings()->first();

        if(intval($old_mapping->OriginSurveyID) !== intval($this->OriginSurveyID) ){
            $result->error('you need to specify the same origin survey for all your mappings');
        }
        return $result;
    }

    /**
     * @return ISurveyQuestionTemplate
     */
    public function getOriginField()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'OriginField')->getTarget();
    }

    /**
     * @return ISurveyTemplate
     */
    public function getOriginSurvey()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'OriginSurvey')->getTarget();
    }
}