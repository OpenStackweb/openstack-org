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
final class NewSurveyDataAutopopulationStrategy implements ISurveyAutopopulationStrategy
{

    /**
     * @param ISurvey $survey
     * @param ISurveyBuilder $survey_builder
     * @param ISurveyManager $manager
     * @return void
     */
    public function autoPopulate(ISurvey $survey, ISurveyBuilder $survey_builder, ISurveyManager $manager)
    {
        $template   = $survey->template();
        $owner      = $survey->createdBy();
        $mappings   = $template->getAutopopulationMappings();

        $old_survey = null;

        foreach($mappings as $mapping) {
            if (!$mapping instanceof INewSurveyMigrationMapping) {
                continue;
            }
            $origin_survey_template = $mapping->getOriginSurvey();

            $old_survey = Survey::get()
                ->filter( array
                    (
                        'CreatedByID' => $owner->getIdentifier(),
                        'TemplateID' => $origin_survey_template->ID
                    )
                )
                ->exclude('ID', $survey->ID)
                ->sort('Created', 'DESC')
                ->first();
            if(is_null($old_survey)) return;
            $origin_field           = $mapping->getOriginField();
            $question               = $mapping->getTargetQuestion();
            if(is_null($question)) continue;
            $step_template          = $question->step();
            if(is_null($step_template)) continue;
            $step                   = $survey->getStep($step_template->title());
            if(is_null($step)) continue;
            if(!$step instanceof ISurveyRegularStep) continue;

            $old_answer = $old_survey->findAnswerByQuestion($origin_field);
            if(is_null($old_answer)) continue;
            $step->addAnswer($survey_builder->buildAnswer($question, SurveyAnswerValueTranslator::translate($old_answer->value(), $origin_field, $question)));
        }

        //check if we need to auto-populate entities

        foreach($template->getEntities() as $template_entity_survey)
        {
            if($template_entity_survey->belongsToDynamicStep() && $template_entity_survey->shouldPrepopulateWithFormerData())
            {
                $mappings = $template_entity_survey->getAutopopulationMappings();

                $dyn_step = $survey->getStep($template_entity_survey->getDynamicStepTemplate()->title());

                if(is_null($dyn_step)) continue;

                foreach (EntitySurvey::get()->filter(array('ParentID' => $old_survey->ID)) as $old_entity) {

                    $entity_survey = $manager->buildEntitySurvey($dyn_step, $owner->getIdentifier());

                    foreach ($mappings as $mapping)
                    {
                        if (!$mapping instanceof INewSurveyMigrationMapping)
                        {
                            continue;
                        }

                        $origin_survey_template = $mapping->getOriginSurvey();
                        $origin_field           = $mapping->getOriginField();
                        $question               = $mapping->getTargetQuestion();
                        $step_template          = $question->step();
                        $step                   = $entity_survey->getStep($step_template->title());

                        if (!$step instanceof ISurveyRegularStep)
                        {
                            continue;
                        }
                        $old_answer = $old_entity->findAnswerByQuestion($origin_field);
                        if(is_null($old_answer)) continue;
                        $step->addAnswer($survey_builder->buildAnswer($question, SurveyAnswerValueTranslator::translate($old_answer->value(), $origin_field, $question)));
                    }
                }
            }

        }
    }

}