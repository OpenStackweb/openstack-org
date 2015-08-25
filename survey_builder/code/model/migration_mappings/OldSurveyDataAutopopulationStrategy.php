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
class OldSurveyDataAutopopulationStrategy implements ISurveyAutopopulationStrategy
{

    /**
     * @param ISurvey $survey
     * @param ISurveyBuilder $survey_builder
     * @param ISurveyManager $manager
     */
    public function autoPopulate(ISurvey $survey, ISurveyBuilder $survey_builder , ISurveyManager $manager)
    {
        $template   = $survey->template();
        $owner      = $survey->createdBy();
        $mappings   = $template->getAutopopulationMappings();
        // get former survey ...
        $old_survey = DeploymentSurvey::get()->filter('MemberID', $owner->getIdentifier())->sort('Created', 'DESC')->first();

        foreach($mappings as $mapping)
        {
            if(!$mapping instanceof IOldSurveyMigrationMapping) continue;

            $origin_table_name = $mapping->getOriginTableName();
            $origin_field_name = $mapping->getOriginFieldName();
            $question          = $mapping->getTargetQuestion();
            $step_template     = $question->step();
            $step              = $survey->getStep($step_template->title());

            if(!$step instanceof ISurveyRegularStep) continue;

            if($origin_table_name === 'DeploymentSurvey')
            {
                $old_data = $old_survey->$origin_field_name;
                // old data is only a label, we need to find out the value
                $data = $old_data;
                if($question instanceof IMultiValueQuestionTemplate)
                {
                    $data = '';
                    $old_data = explode(',', $old_data);
                    foreach($old_data as $od)
                    {
                        $v = $question->getValueByValue($od);
                        if(is_null($v)) continue;
                        if(!empty($data)) $data.=',';
                        $data.= $v->getIdentifier();
                    }
                }
                if(empty($data)) continue;
                $step->addAnswer($survey_builder->buildAnswer($question, $data));
            }
            if($origin_table_name === 'AppDevSurvey')
            {
                $app_dev_survey = $old_survey->AppDevSurveys()->first();
                if(!$app_dev_survey) continue;
                $data          = $app_dev_survey->$origin_field_name;
                if(empty($data)) continue;
                $step->addAnswer($survey_builder->buildAnswer($question, $data));
            }
        }

        //check if we need to auto-populate entities

        foreach($template->getEntities() as $entity_survey)
        {
            if($entity_survey->belongsToDynamicStep() && $entity_survey->shouldPrepopulateWithFormerData())
            {
                $mappings = $template->getAutopopulationMappings();

                $dyn_step = $survey->getStep($entity_survey->getDynamicStepTemplate()->title());

                if(is_null($dyn_step)) continue;

                foreach ($old_survey->Deployments() as $old_deployment) {

                    $entity_survey = $manager->buildEntitySurvey($dyn_step, $owner->getIdentifier());

                    /*foreach ($mappings as $mapping)
                    {
                        if (!$mapping instanceof IOldSurveyMigrationMapping)
                        {
                            continue;
                        }

                        $origin_table_name = $mapping->getOriginTableName();
                        $origin_field_name = $mapping->getOriginFieldName();
                        $question          = $mapping->getTargetQuestion();
                        $step_template     = $question->step();
                        $step              = $entity_survey->getStep($step_template->title());

                        if (!$step instanceof ISurveyRegularStep)
                        {
                            continue;
                        }


                        if ($origin_table_name === 'Deployment')
                        {
                            $data = $old_deployment->$origin_field_name;
                            if (empty($data)) {
                                continue;
                            }
                            $step->addAnswer($survey_builder->buildAnswer($question, $data));
                        }
                    }*/
                }
            }
        }
    }
}