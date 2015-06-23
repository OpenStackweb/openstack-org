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
 * Interface ISurveyDynamicEntityStep
 */
interface ISurveyDynamicEntityStep extends ISurveyStep {

    /**
     * @return ISurveyDynamicEntityStepTemplate
     */
    public function template();

    /**
     * @return IEntitySurvey[]
     */
    public function getEntitySurveys();

    /**
     * @param IEntitySurvey $entity_survey
     * @return void
     */
    public function addEntitySurvey(IEntitySurvey $entity_survey);

    /**
     * @param int $entity_survey_id
     * @return void
     */
    public function removeEntitySurveyById($entity_survey_id);

    /**
     * @return void
     */
    public function clearEntitiesSurvey();

    /**
     * @param int $entity_survey_id
     * @return null|IEntitySurvey
     */
    public function getEntitySurvey($entity_survey_id);

}