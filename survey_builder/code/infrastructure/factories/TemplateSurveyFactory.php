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
class TemplateSurveyFactory implements ISurveyTemplateFactory
{

    /**
     * @param $title
     * @param null $start_date
     * @param null $end_date
     * @param bool|false $enabled
     * @return ISurveyTemplate
     */
    public function  build($title, $start_date = null, $end_date = null, $enabled = false)
    {
        $template = new SurveyTemplate;
        $template->Title = $title;
        return $template;
    }

    /**
     * @param ISurveyStep $original_step
     * @return ISurveyStep
     */
    public function  cloneStep(ISurveyStep $original_step)
    {
        $class_name            = $original_step->ClassName;
        $clone                 = new $class_name;
        $clone->Name           = $original_step->Name;
        $clone->Content        = $original_step->Content;
        $clone->FriendlyName   = $original_step->FriendlyName;
        $clone->Order          = $original_step->Order;
        $clone->SkipStep       = $original_step->SkipStep;
        if($original_step instanceof SurveyDynamicEntityStepTemplate) {
            $clone->AddEntityText    = $original_step->AddEntityText;
            $clone->DeleteEntityText = $original_step->DeleteEntityText;
            $clone->EditEntityText   = $original_step->EditEntityText;
            $clone->EntityIconID     = $original_step->EntityIconID;
        }
        return $clone;
    }

    /**
     * @param ISurveyTemplate $original_template
     * @param int|null $parent_id
     * @return ISurveyTemplate
     */
    public function cloneTemplate(ISurveyTemplate $original_template, $parent_id = null)
    {
        $class_name   = $original_template->ClassName;
        $clone        = new $class_name;
        $clone->Title = $original_template->Title;
        if($clone instanceof EntitySurveyTemplate)
        {
            $clone->EntityName     = $original_template->EntityName;
            $clone->UseTeamEdition = $original_template->UseTeamEdition;
            $clone->ParentID       = $parent_id;
        }
        return $clone;
    }

    /**
     * @param ISurveyQuestionTemplate $question
     * @return ISurveyQuestionTemplate
     */
    public function cloneQuestion(ISurveyQuestionTemplate $question)
    {
        $class_name                     = $question->ClassName;
        $clone                          = new $class_name;
        $clone->Name                    = $question->Name;
        $clone->Label                   = $question->Label;
        $clone->Order                   = $question->Order;
        $clone->Mandatory               = $question->Mandatory;
        $clone->ReadOnly                = $question->ReadOnly;
        $clone->ShowOnSangriaStatistics = $question->ShowOnSangriaStatistics;
        $clone->ShowOnPublicStatistics  = $question->ShowOnPublicStatistics;

        if($question instanceof ISingleValueTemplateQuestion)
        {
            $clone->InitialValue = $question->InitialValue;
        }
        if($question instanceof SurveyDropDownQuestionTemplate)
        {
            $clone->IsMultiSelect = $question->IsMultiSelect;
            $clone->IsCountrySelector = $question->IsCountrySelector;
            $clone->UseChosenPlugin = $question->UseChosenPlugin;
        }
        if($question instanceof SurveyLiteralContentQuestionTemplate)
        {
            $clone->Content = $question->Content;
        }
        if($question instanceof SurveyRankingQuestionTemplate)
        {
            $clone->Intro          = $question->Intro;
            $clone->MaxItemsToRank = $question->MaxItemsToRank;
        }
        return $clone;
    }

    /**
     * @param IQuestionValueTemplate $value
     * @return IQuestionValueTemplate
     */
    public function cloneQuestionValue(IQuestionValueTemplate $value){
        $class_name = $value->ClassName;
        $clone      = new $class_name;

        $clone->Value = $value->Value;
        $clone->Label = $value->Label;
        $clone->Order = $value->Order;
        if($value instanceof SurveyQuestionRowValueTemplate) {
            $clone->IsAdditional = $value->IsAdditional;
        }
        return $clone;
    }
}