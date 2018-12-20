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
class SurveyRadioButtonMatrixTemplateQuestionUIBuilder extends AbstractSurveyQuestionTemplateUIBuilder
{

    /**
     * @param ISurveyStep $current_step
     * @param ISurveyQuestionTemplate $question
     * @param ISurveyAnswer $answer
     * @return FormField
     */
    public function build(ISurveyStep $current_step, ISurveyQuestionTemplate $question, ?ISurveyAnswer $answer)
    {
        $field  = new SurveyRadioButtonMatrixField($question->name(), GetTextTemplateHelpers::_t("survey_template", $question->label()), $question);

        if(!is_null($answer)){
            $field->setAnswer($answer);
        }
        if($question->isReadOnly()) $field->setDisabled(true);
        if($question->isMandatory())
        {
            $field->setValidationAttribute('data-rule-radio_button_matrix_required', $field->name);
            $field->setValidationAttribute('data-msg-radio_button_matrix_required', GetTextTemplateHelpers::_t("survey_ui",'you must select at least one item (%s)', $question->name()));
        }
        $this->buildDependantRules($current_step, $question, $field);
        return $field;
    }
}