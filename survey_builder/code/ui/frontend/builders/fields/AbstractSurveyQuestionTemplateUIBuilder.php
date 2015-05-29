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

/**
 * Class AbstractSurveyQuestionTemplateUIBuilder
 */
abstract class AbstractSurveyQuestionTemplateUIBuilder
    implements ISurveyQuestionTemplateUIBuilder {

    protected function buildDependantJS(ISurveyQuestionTemplate $question, FormField $field){
        //depends : check visibility
        $depends = $question->DependsOn();

        if(count($depends) > 0){
            //hide and set js rule
            $field->addExtraClass('hidden');
            $question_id = $question->name();

            $js = "jQuery(document).ready(function($){

                    var form              = $('.survey_step_form');
                    var form_id           = form.attr('id');
                    var clickable_fields  = [];
                    var selectable_fields = [];
                    ";

            foreach($depends as $d) {

                $option_id = $d->name();

                if($d instanceof ISurveyClickableQuestion) {
                    if ($d instanceof IMultiValueQuestionTemplate && intval($d->ValueID) > 0) {
                        $value = $d->getValueById(intval($d->ValueID));
                        $option_id .= '_' . str_replace(' ', '', $value->Label);
                    }
                    $js .= " clickable_fields.push($('#'+form_id+'_{$option_id}'));";
                }

                if($d instanceof ISurveySelectableQuestion) {

                    if ($d instanceof IMultiValueQuestionTemplate && intval($d->ValueID) > 0) {
                        $value = $d->getValueById(intval($d->ValueID));
                        $js .= " selectable_fields.push({ddl : $('#'+form_id+'_{$option_id}'), label: '{$value->Label}' });";
                    }

                }
            }

            $js .= "for(var i = 0 ; i < selectable_fields.length; i++ ){
                form.survey_validation_rules('addRequiredAnswer4SelectableGroup', [ selectable_fields[i].ddl ], $('#{$question_id}'), selectable_fields[i].label );
            }";
            $js .= "if(clickable_fields.length > 0 ) form.survey_validation_rules('addRequiredAnswer4ClickableGroup', clickable_fields, $('#{$question_id}') ); });";
            Requirements::customScript($js);
        }
    }
}