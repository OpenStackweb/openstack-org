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
    public function build(ISurveyStep $step, $action, $form_name ='SurveyStepForm')
    {
        $form = parent::build($step, $action, $form_name);
        $entity_survey = $step->survey();
        if
        (
            $entity_survey instanceof IEntitySurvey
        )
        {
            $fields = $form->Fields();
            $first  = $fields->first();

            if
            (
                $entity_survey->isTeamEditionAllowed() &&
                $entity_survey->createdBy()->getIdentifier() === Member::currentUserID() &&
                $step->template()->order() === 1 // only show on first step
            )
            {
                $fields->insertBefore
                (
                    $team_field = new EntitySurveyEditorTeamField('EditorTeam', '', $entity_survey),
                    $first->getName()
                );
                $team_field->setForm($form);
                $first = $team_field;
            }

            $edition_info_panel = '<div class="container editor-info-panel"><div class="row">Created by <b>'.$entity_survey->createdBy()->getEmail().'</b></div>';
            $edition_info_panel .= '<div class="row">Edited by <b>'.$entity_survey->EditedBy()->getEmail().'</b></div></div>';
            $fields->insertBefore
            (
                new LiteralField('owner_label',$edition_info_panel),
                $first->getName()
            );

            if
            (
                $step->template()->order() > 1 &&
                $previous_step = $entity_survey->getPreviousStep($step->template()->title())
            )
            {
                $request = Controller::curr()->getRequest();
                $step                 = $request->param('STEP_SLUG');
                $sub_step             = $request->param('SUB_STEP_SLUG');
                $entity_survey_id     = intval($request->param('ENTITY_SURVEY_ID'));
                $prev_step_url        = Controller::join_links
                (
                    Director::absoluteBaseURL(),
                    'surveys/current/',
                    $step,
                    'edit',
                    $entity_survey_id,
                    $previous_step->template()->title()
                );
                // add prev button
                $actions = $form->Actions();
                $btn = $actions->offsetGet(0);
                $actions->insertBefore
                (
                    $prev_action = new FormAction('','Prev Step'),
                    $btn->name
                );

                $prev_action->addExtraClass('entity-survey-prev-action');
                $prev_action->setAttribute('data-prev-url', $prev_step_url);
            }

        }
        return $form;
    }
}