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
 * Class SurveyBuilder
 */
final class SurveyBuilder implements ISurveyBuilder {

    /**
     * @param ISurveyTemplate $template
     * @param  $owner
     * @return ISurvey
     */
    public function build(ISurveyTemplate $template, $owner)
    {
       $survey              = new Survey();
       $survey->TemplateID  = $template->getIdentifier();
       $survey->CreatedByID = $owner->getIdentifier();

       $i = 0;
       foreach($template->getSteps() as $step_template){
           ++$i;
           $new_step = null;
           if($step_template instanceof SurveyDynamicEntityStepTemplate){
               $new_step = new SurveyDynamicEntityStep;
           }
           else if($step_template instanceof SurveyRegularStepTemplate){
               $new_step = new SurveyRegularStep();
           }
           else{
               $new_step = new SurveyStep;
           }
           $new_step->TemplateID = $step_template->getIdentifier();
           $survey->addStep($new_step);
           if($i == 1) {
               $survey->registerCurrentStep($new_step);
               $survey->registerAllowedMaxStep($new_step);
           }
       }
       return $survey;
    }

    /**
     * @param ISurvey $parent
     * @param IEntitySurveyTemplate $template
     * @param $owner
     * @return EntitySurvey
     */
    public function buildEntitySurvey(ISurvey $parent, IEntitySurveyTemplate $template, $owner)
    {
        $survey              = new EntitySurvey();
        $survey->TemplateID  = $template->getIdentifier();
        $survey->CreatedByID = $owner->getIdentifier();
        $survey->ParentID    = $parent->getIdentifier();
        $i = 0;
        foreach($template->getSteps() as $step_template){
            ++$i;
            $new_step = $this->buildStep($step_template);
            $survey->addStep($new_step);
            if($i == 1) {
                $survey->registerCurrentStep($new_step);
                $survey->registerAllowedMaxStep($new_step);
            }
        }
        return $survey;
    }

    /**
     * @param ISurveyQuestionTemplate $question
     * @param mixed $answer_value
     * @return ISurveyAnswer
     */
    public function buildAnswer(ISurveyQuestionTemplate $question, $answer_value)
    {
        $answer = new SurveyAnswer();
        if(is_array($answer_value) ){
            $answer_value = str_replace('{comma}', ',', $answer_value);
            $answer->Value = implode(',', $answer_value);
        }
        else{
            $answer->Value = $answer_value;
        }
        $answer->QuestionID = $question->getIdentifier();
        return $answer;
    }

    /**
     * @param ISurveyStepTemplate $step_template
     * @return ISurveyStep
     */
    public function buildStep(ISurveyStepTemplate $step_template)
    {
        $new_step = null;

        if($step_template instanceof SurveyDynamicEntityStepTemplate){
            $new_step = new SurveyDynamicEntityStep;
        }
        else if($step_template instanceof SurveyRegularStepTemplate){
            $new_step = new SurveyRegularStep();
        }
        else{
            $new_step = new SurveyStep;
        }

        $new_step->TemplateID = $step_template->getIdentifier();

        return $new_step;
    }
}