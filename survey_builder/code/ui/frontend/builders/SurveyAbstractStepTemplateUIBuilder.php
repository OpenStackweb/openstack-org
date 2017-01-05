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
abstract class SurveyAbstractStepTemplateUIBuilder implements ISurveyStepUIBuilder
{
    /**
     * @var string
     */
    protected $next_btn_title;

    public function __construct()
    {
        $this->next_btn_title = _t('SurveyBuilder.ActionSaveStep', 'Save &amp; Continue');
    }

    /**
     * @param string $next_btn_title
     * @return $this
     */
    public function setNextButtonTitle($next_btn_title){
        $this->next_btn_title = $next_btn_title;
        return $this;
    }

    /**
     * @param ISurveyStep $previous_step
     * @return String
     */
    protected function getPreviousStepUrl(ISurveyStep $previous_step){
        $prev_step_url = Controller::join_links
        (
            Director::absoluteBaseURL(),
            Controller::curr()->Link(),
            $previous_step->template()->title()
        );
        return $prev_step_url;
    }

    /**
     * @param string $action
     * @param ISurveyStep $step
     * @return array
     */
    protected function buildActions($action, ISurveyStep $step){
        $survey               = $step->survey();
        $save_later_btn_title = _t('SurveyBuilder.ActionSaveAndComeBack', 'Save &amp; Come Back Later');
        $previous_step        = $survey->getPreviousStep($step->template()->title());
        $array_actions        = [];

        if(!is_null($previous_step))
        {

            // add prev button
            $prev_action = new FormAction('PrevStep', _t('SurveyBuilder.ActionGoBack', 'Go Back'));
            $prev_action->setButtonContent("<i class=\"fa fa-chevron-left\" aria-hidden=\"true\"></i>&nbsp;". _t('SurveyBuilder.ActionGoBack', 'Go Back'));
            $prev_action->addExtraClass('btn go-back-action-btn');
            $prev_action->setAttribute('data-prev-url', $this->getPreviousStepUrl($previous_step));
            $prev_action->setUseButtonTag(true);
            $array_actions[] = $prev_action;
        }

        $save_later_action = LinkFormAction::create('save_later')->setTitle($save_later_btn_title);
        $save_later_action->setCSSClass('save-later-action-btn');
        // default action btn
        $default_action    = FormAction::create($action)->setTitle($this->next_btn_title);
        $default_action->addExtraClass("btn default-action-btn");
        $default_action->setUseButtonTag(true);
        $default_action->setButtonContent($this->next_btn_title."&nbsp;<i class=\"fa fa-chevron-right\" aria-hidden=\"true\"></i>");

        $array_actions[]   = $save_later_action;
        $array_actions[]   = $default_action;

        return [$default_action, new FieldList( $array_actions )];
    }

    /**
     * @param string $form_name
     * @param $fields
     * @param $actions
     * @param $step
     * @param $validator
     * @return DynamicStepForm
     */
    abstract protected function buildForm($form_name, $fields, $actions, $step, $validator);
}