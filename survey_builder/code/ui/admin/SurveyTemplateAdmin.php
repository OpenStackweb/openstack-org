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

class SurveyTemplateAdmin extends ModelAdmin {

    public static $managed_models = array(
        'SurveyTemplate',
    );

    public $showImportForm = false;
    private static $url_segment = 'SurveyBuilder';
    private static $menu_title  = 'Survey Builder';

    public function init()
    {
        parent::init();

        $script = '
        var origin_fields =
        {
            "DeploymentSurvey" : [],
            "AppDevSurvey" : [],
            "Deployment" : []
        };
        ';

        foreach(DeploymentSurveyFields::toArray() as $key)
        {
            $script .= sprintf("origin_fields.DeploymentSurvey.push('%s');", $key);
        }

        foreach(AppDevSurveyFields::toArray() as $key)
        {
            $script .= sprintf("origin_fields.AppDevSurvey.push('%s');", $key);
        }

        foreach(DeploymentFields::toArray() as $key)
        {
            $script .= sprintf("origin_fields.Deployment.push('%s');", $key);
        }

        $path = ASSETS_PATH."/survey.builder.origin.fields.js";
        $custom_script_file = fopen($path, "w") or die("Unable to open file!");
        fwrite($custom_script_file, $script);
        fclose($custom_script_file);

        Requirements::javascript('assets/survey.builder.origin.fields.js');

        $templates = SurveyTemplate::get();
        $script_data = 'var templates = {}; var origin_field_id = null;';
        foreach($templates as $template) {
            $steps     = $template->Steps()->filter('ClassName','SurveyRegularStepTemplate');
            $script_data .= "templates[{$template->ID}] = { questions : [] };";
            $questions = array();
            foreach ($steps as $step) {
                foreach ($step->getQuestions() as $question) {
                    if ($question instanceof SurveyLiteralContentQuestionTemplate) {
                        continue;
                    }
                    $questions[$question->ID] = $step->Name . ' -> ' . $question->Name;

                    $script_data .= "templates[{$template->ID}].questions.push({ id: $question->ID, name: '{$step->Name} -> {$question->Name}'});";
                }
            }
        }

        $path = ASSETS_PATH."/templates.data.js";
        $custom_script_file = fopen($path, "w") or die("Unable to open file!");
        fwrite($custom_script_file, $script_data);
        fclose($custom_script_file);
        Requirements::javascript('assets/templates.data.js');

        Requirements::javascript('survey_builder/js/active_records/new.datamodel.survey.migration.mapping.js');
    }

    public function getEditForm($id = null, $fields = null) {

        $form = parent:: getEditForm($id, $fields);

        if($this->modelClass === 'SurveySingleValueValidationRule') {
            $gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
            $config = $gridField->getConfig();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();

            $multi_class_selector->setClasses( array(
                    'SurveyRangeValidationRule'     => 'Range Validation Rule' ,
                    'SurveyNumberValidationRule'    => 'Number Validation Rule' ,
                    'SurveyMinLengthValidationRule' => 'Min Length Validation Rule' ,
                    'SurveyMaxLengthValidationRule' => 'Max Length Validation Rule' ,
                    'SurveyCustomValidationRule'    => 'Custom Validation Rule' ,
                )
            );

            $config->addComponent($multi_class_selector);
        }
        if($this->modelClass === 'SurveyTemplate') {
            $gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
            $config = $gridField->getConfig();
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->addComponent(new GridFieldCloneSurveyTemplateAction());
        }
        return $form;
    }

    public function getList() {
        $list =  parent::getList();
        if($this->modelClass === 'SurveyTemplate') {
            $list = $list->filter(array( 'ClassName' => 'SurveyTemplate'));
        }
        return $list;
    }

}