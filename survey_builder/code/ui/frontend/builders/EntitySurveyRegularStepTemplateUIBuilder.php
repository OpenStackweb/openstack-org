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
class EntitySurveyRegularStepTemplateUIBuilder extends SurveyRegularStepTemplateUIBuilder
{
    /**
     * @param ISurveyStep $step
     * @param string $action
     * @param string $form_name
     * @return Form
     */
    public function build(ISurveyStep $step, $action, $form_name = 'SurveyStepForm')
    {

        if (!$step->survey()->isLastStep())
            $this->setNextButtonTitle(sprintf('STEP %s', $step->survey()->getCurrentStepIndexNice() + 1));

        $form          = parent::build($step, $action, $form_name);
        $entity_survey = $step->survey();

        if
        (
            $entity_survey instanceof IEntitySurvey &&
            $entity_survey->isTeamEditionAllowed() &&
            $entity_survey->createdBy()->getIdentifier() === Member::currentUserID() &&
            $entity_survey->isFirstStep() // only show on first step
        ) {

            Requirements::javascript('survey_builder/js/entity.survey.editor.team.field.js');
        }
        return $form;
    }

    /**
     * @param string $form_name
     * @param $fields
     * @param $actions
     * @param $step
     * @param $validator
     * @return EntityRegularStepForm
     */
    protected function buildForm($form_name, $fields, $actions, $step, $validator)
    {
        return new EntityRegularStepForm(Controller::curr(), $form_name, $fields, $actions, $step, $validator);
    }

    /**
     * @param ISurveyStep $previous_step
     * @return String
     */
    protected function getPreviousStepUrl(ISurveyStep $previous_step)
    {
        $request = Controller::curr()->getRequest();
        $step = $request->param('STEP_SLUG');

        if (empty($step))
            $step = $request->requestVar('STEP_SLUG');

        if (empty($step))
            throw new LogicException('step empty! - member_id %s', Member::currentUserID());

        $entity_survey_id = intval($request->param('ENTITY_SURVEY_ID'));
        $prev_step_url = Controller::join_links
        (
            Director::absoluteBaseURL(),
            Controller::curr()->Link(),
            $step,
            'edit',
            $entity_survey_id,
            $previous_step->template()->title()
        );
        return $prev_step_url;
    }
}