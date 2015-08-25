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

class SurveyRankingQuestionTemplateUIBuilder extends AbstractSurveyQuestionTemplateUIBuilder {

    /**
     * @param ISurveyStep $current_step
     * @param ISurveyQuestionTemplate $question
     * @param ISurveyAnswer $answer
     * @return FormField
     */
    public function build(ISurveyStep $current_step, ISurveyQuestionTemplate $question, ISurveyAnswer $answer)
    {
        $options = array();
        foreach($question->Values()->sort('Order') as $val)
        {
            $options[$val->ID] = empty($val->Label)?$val->Value:$val->Label;
        }

        $field  = new SurveyRankingField($question->name(), $question->label(), $options,  $value = '' , $form=null, $emptyString=null, $question);

        if($question->isReadOnly()) $field->setDisabled(true);
        if($question->isMandatory())
        {
           $field->setValidationAttribute('data-rule-ranking_required', $field->ID());
           $field->setValidationAttribute('data-msg-ranking_required', sprintf('you must select at least one item (%s)', $question->name()));
        }
        if(!is_null($answer)){
            $field->setValue($answer->value());
        }
        $this->buildDependantRules($current_step, $question, $field);
        return $field;
    }
}