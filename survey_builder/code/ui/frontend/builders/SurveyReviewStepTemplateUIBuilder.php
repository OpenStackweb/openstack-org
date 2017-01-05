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
final class SurveyReviewStepTemplateUIBuilder
    extends SurveyAbstractStepTemplateUIBuilder
    implements ISurveyStepUIBuilder
{

    public function __construct()
    {
        parent::__construct();
        $this->next_btn_title = _t('SurveyBuilder.ActionSubmitSurvey', 'Submit Your Survey');
    }

    /**
     * @param ISurveyStep $step
     * @param string $action
     * @param string $form_name
     * @return Form
     */
    public function build(ISurveyStep $step, $action, $form_name = 'SurveyStepForm')
    {
        $fields = new FieldList();
        $fields->add(new HiddenField('survey_id', 'survey_id', $step->survey()->getIdentifier()));
        $fields->add(new HiddenField('step_id', 'step_id', $step->getIdentifier()));
        $validator = null;
        list($default_action, $actions) = $this->buildActions($action, $step);
        $form = $this->buildForm($form_name, $fields, $actions, $step, $validator);
        $form->setDefaultAction($default_action);
        $form->setAttribute('class','survey_step_form');
        $form->disableSecurityToken();
        return $form;
    }

    /**
     * @param ISurveyStep $previous_step
     * @return String
     */
    protected function getPreviousStepUrl(ISurveyStep $previous_step){
        $prev_step_url        = Controller::join_links
        (
            Director::absoluteBaseURL(),
            Controller::curr()->Link(),
            $previous_step->template()->title()
        );
        return $prev_step_url;
    }

    /**
     * @param string $form_name
     * @param $fields
     * @param $actions
     * @param $step
     * @param $validator
     * @return DynamicStepForm
     */
    protected function buildForm($form_name, $fields, $actions, $step, $validator)
    {
        return new ReviewStepForm
        (
            Controller::curr(),
            $form_name,
            $fields,
            $actions,
            $step,
            $validator = array()
        );
    }
}