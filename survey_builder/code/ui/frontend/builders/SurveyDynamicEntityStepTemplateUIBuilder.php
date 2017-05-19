<?php
/**
 * Copyright 2017 OpenStack Foundation
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

class SurveyDynamicEntityStepTemplateUIBuilder
    extends SurveyAbstractStepTemplateUIBuilder
    implements ISurveyStepUIBuilder {
    /**
     * @param ISurveyStep $step
     * @param string $action
     * @param string $form_name
     * @return Form
     */
    public function build(ISurveyStep $step, $action, $form_name ='SurveyStepForm')
    {
        $fields    = new FieldList();

        $fields->add(new HiddenField('survey_id', 'survey_id', $step->survey()->getIdentifier()));
        $fields->add(new HiddenField('step_id', 'step_id', $step->getIdentifier()));

        $content = $step->template()->content();
        if(!empty($content))
            $fields->add(new LiteralField('content',  GetTextTemplateHelpers::_t("survey_template", $content)));

        $validator = null;

        list($default_action, $actions) = $this->buildActions($action, $step);

        $form = $this->buildForm($form_name, $fields, $actions, $step, $validator);
        $form->setTemplate('DynamicEntityStepForm');
        $form->setAttribute('class','survey_step_form');
        $form->setDefaultAction($default_action);
        return $form;
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
       return new DynamicStepForm(Controller::curr(), $form_name, $fields, $actions, $step, $validator);
    }
}