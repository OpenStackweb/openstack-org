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
 * Interface ISurveyTemplate
 */
interface ISurveyTemplate extends IEntity {

    /**
     * @return boolean
     */
    public function isEnabled();

    /**
     * @return boolean
     */
    public function isVoid();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return IFoundationMember
     */
    public function owner();

    /**
     * @return ISurveyStepTemplate[]
     */
    public function getSteps();

    /**
     * @param ISurveyStepTemplate $step
     * @return void
     */
    public function addStep(ISurveyStepTemplate $step);

    /**
     * @return ISurveyStepTemplate
     */
    public function getDefaultStep();

    /**
     * @return ISurveyStepTemplate
     */
    public function getLastStep();

    /**
     * @return bool
     */
    public function shouldPrepopulateWithFormerData();

    /**
     * @return IMigrationMapping[]
     */
    public function getAutopopulationMappings();

    /**
     * @return IEntitySurveyTemplate[]
     */
    public function getEntities();

    /**
     * @return string
     */
    public function QualifiedName();

}