<?php

/**
 * Copyright 2016 OpenStack Foundation
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
 * Class ReviewStepForm
 */
class ReviewStepForm extends ThankYouStepForm
{
    /**
     * @return ISurvey
     */
    public function Survey(){
        return $this->step->survey();
    }

    public function SurveyStepClassIcon($step_name){
        return $this->Controller()->SurveyStepClassIcon($step_name);
    }

    public function Link(){
        $action = func_num_args() ? func_get_arg(0) : null;
        return $this->Controller()->Link($action);
    }

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'Link',
        'Survey',
        'SurveyStepClassIcon',
    );

}