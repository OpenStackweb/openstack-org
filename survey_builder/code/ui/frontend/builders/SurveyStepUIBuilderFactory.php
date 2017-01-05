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
 * Class SurveyStepUIBuilderFactory
 */
final class SurveyStepUIBuilderFactory {
    /**
     * @var SurveyStepUIBuilderFactory
     */
    private static $instance;

    private function __construct(){}

    private function __clone(){}

    /**
     * @return SurveyStepUIBuilderFactory
     */
    public static function getInstance(){
        if(!is_object(self::$instance)){
            self::$instance = new SurveyStepUIBuilderFactory();
        }
        return self::$instance;
    }

    /**
     * @param ISurveyStep $step
     * @return null|ISurveyStepUIBuilder
     */
    public function build(ISurveyStep $step)
    {
        if($step->template() instanceof ISurveyRegularStepTemplate)
        {
            if($step->survey() instanceof IEntitySurvey)
                return new EntitySurveyRegularStepTemplateUIBuilder;

            return new SurveyRegularStepTemplateUIBuilder;
        }
        if($step->template() instanceof ISurveyDynamicEntityStepTemplate)
            return new SurveyDynamicEntityStepTemplateUIBuilder;
        if($step->template() instanceof ISurveyReviewStepTemplate)
            return new SurveyReviewStepTemplateUIBuilder;
        if($step->template() instanceof ISurveyThankYouStepTemplate)
            return new SurveySurveyThankYouStepTemplateUIBuilder;
        return null;
    }
}