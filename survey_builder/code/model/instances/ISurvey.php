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

interface ISurvey extends IEntity {

    /**
     * @return ISurveyStep
     */
    public function allowedMaxStep();

    /**
     * @param ISurveyStep $max_step
     * @return void
     */
    public function registerAllowedMaxStep(ISurveyStep $max_step);

    /**
     * @return ISurveyStep
     */
    public function currentStep();

    /**
     * @param ISurveyStep $current_step
     * @return void
     */
    public function registerCurrentStep(ISurveyStep $current_step);

    /**
     * @return ISurveyStep[]
     */
    public function getSteps();

    /**
     * @param ISurveyStep $step
     * @return void
     */
    public function addStep(ISurveyStep $step);

    /**
     * @return ISurveyTemplate
     */
    public function template();

    /**
     * @return ICommunityMember
     */
    public function createdBy();

    /**
     * @param string $step_name
     * @return bool
     */
    public function isAllowedStep($step_name);
}