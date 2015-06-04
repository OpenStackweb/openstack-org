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
 * Class SurveyRegularStepTemplateUIBuilder
 */
class SurveyRegularStepTemplateUIBuilder
    implements ISurveyStepUIBuilder
{

    /**
     * @param ISurveyStep $step
     * @param string $action
     * @return Form
     */
    public function build(ISurveyStep $step, $action)
    {
        Requirements::customScript('jQuery(document).ready(function($) {
            var form = $(".survey_step_form");
            form.validate();
        });');

        $fields = new FieldList();

        $fields->add(new LiteralField('content', $step->template()->content()));

        foreach ($step->template()->getQuestions() as $q) {

            $type          = $q->Type();
            $builder_class = $type.'UIBuilder';
            // @ISurveyQuestionTemplateUIBuilder
            $builder = Injector::inst()->create($builder_class);
            $field   = $builder->build($step, $q, $step->getAnswerByTemplateId($q->getIdentifier()));
            $fields->add($field);
        }

        $validator = null;

        $fields->add(new HiddenField('survey_id', 'survey_id', $step->survey()->getIdentifier()));
        $fields->add(new HiddenField('step_id', 'step_id', $step->getIdentifier()));

        $actions   = new FieldList(
            FormAction::create($action)->setTitle("Next")
        );

        $form =  new RegularStepForm(Controller::curr(), 'SurveyStepForm', $fields, $actions, $step, $validator);
        $form->setAttribute('class','survey_step_form');
        return $form;
    }
}