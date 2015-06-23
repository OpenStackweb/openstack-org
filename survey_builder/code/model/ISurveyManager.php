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
 * Interface ISurveyManager
 */
interface ISurveyManager {

    /**
     * @param int $template_id
     * @param int $creator_id
     * @return ISurvey
     */
    public function buildSurvey($template_id, $creator_id);

    /**
     * @param ISurveyDynamicEntityStep $step
     * @param int $creator_id
     * @return IEntitySurvey
     */
    public function buildEntitySurvey(ISurveyDynamicEntityStep $step, $creator_id);

    /**
     * @return ISurveyTemplate
     */
    public function getCurrentSurveyTemplate();

    /**
     * @param array $data
     * @param ISurveyStep $current_step
     * @return ISurveyStep
     */
    public function completeStep(ISurveyStep $current_step, array $data);

    /**
     * @param ISurvey $survey
     * @param string $step_name
     * @return void
     */
    public function registerCurrentStep(ISurvey $survey, $step_name);

    /**
     * @param IMessageSenderService $sender_service
     * @param ISurvey $survey
     * @return void
     */
    public function sendFinalStepEmail(IMessageSenderService $sender_service, ISurvey $survey);

    /**
     * @param ISurvey $survey
     * @param ISurveyTemplate $template
     * @return ISurvey
     */
    public function updateSurveyWithTemplate(ISurvey $survey, ISurveyTemplate $template);

    /**
     * @param int $entity_id
     * @param ISurveyDynamicEntityStep $current_step
     * @return void
     */
    public function deleteEntitySurvey(ISurveyDynamicEntityStep $current_step, $entity_id);
}