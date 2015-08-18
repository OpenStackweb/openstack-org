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

class SurveyDropDownQuestionTemplateUIBuilder  extends AbstractSurveyQuestionTemplateUIBuilder
{

    /**
     * @param ISurveyStep $current_step
     * @param ISurveyQuestionTemplate $question
     * @param ISurveyAnswer $answer
     * @return FormField
     */
    public function build(ISurveyStep $current_step, ISurveyQuestionTemplate $question, ISurveyAnswer $answer)
    {
        $values = array();

        if($question->IsCountrySelector){
            $values = CountryCodes::$iso_3166_countryCodes;
            $values['Worldwide']         = 'Worldwide';
            $values['Prefer not to say'] = 'Prefer not to say';
            $values['Too many to list']  = 'Too many to list';
        }
        else
            $values = $question->Values()->sort('Order')->map('ID','Value');

        $default_value = $question->DefaultValue();

        $field  = ($question->IsMultiSelect) ? new MultiDropdownField($question->name(), $question->label(), $values) : new DropdownField($question->name(), $question->label(), $values);
        if($question->isReadOnly()) $field->setDisabled(true);
        if($question->isMandatory())
        {
            $field->setAttribute('data-rule-required','true');
            $field->setAttribute('aria-required','true');
        }

        if($default_value)
        {
            $field->setValue($default_value->ID);
        }

        if(!is_null($answer))
        {
            $field->setValue($answer->value());
        }

        $empty_string = $question->EmptyString;

        if(!empty($empty_string))
        {
            if($question->UseChosenPlugin && $question->IsMultiSelect)
                $field->setAttribute('data-placeholder', $empty_string);
            else
                $field->setEmptyString($empty_string);
        }

        $this->buildDependantRules($current_step, $question, $field);

        if($question->UseChosenPlugin){

            $field->addExtraClass('chosen');
            if(count($question->DependsOn()) === 0) {
                $field->addExtraClass('chosen-visible');
            }
            Requirements::customScript("jQuery(document).ready(function($){
                            var form    = $('.survey_step_form');
                            var form_id = form.attr('id');
                            $('#'+form_id+'_{$question->name()}').chosen({width: '30%'});
                        });
                    ");
        }
        return $field;
    }
}