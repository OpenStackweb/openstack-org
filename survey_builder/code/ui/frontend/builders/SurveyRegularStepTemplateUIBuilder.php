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
            var form = $("#HoneyPotForm_SurveyStepForm");
            form.validate();
        });');

        $fields = new FieldList();
        foreach ($step->template()->getQuestions() as $q) {
            $type          = $q->Type();
            $builder_class = $type.'UIBuilder';
            // @ISurveyQuestionTemplateUIBuilder
            $builder = Injector::inst()->create($builder_class);
            $field   = $builder->build($q);
            $fields->add($field);
        }

        $validator      = null;

        $actions   = new FieldList(
            FormAction::create($action)->setTitle("Next")
        );

        $form =  new HoneyPotForm(Controller::curr(), 'SurveyStepForm', $fields, $actions, $validator);
        return $form;
    }
}