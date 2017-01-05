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
    extends SurveyAbstractStepTemplateUIBuilder
    implements ISurveyStepUIBuilder
{

    /**
     * @param ISurveyStep $step
     * @param string $action
     * @param string $form_name
     * @return Form
     */
    public function build(ISurveyStep $step, $action, $form_name ='SurveyStepForm')
    {
        Requirements::customScript('jQuery(document).ready(function($) {
            var form = $(".survey_step_form");
            form.validate();
        });');

        $fields = new FieldList();

        $content = $step->template()->content();
        if(!empty($content))
            $fields->add(new LiteralField('content', $content));

        if($step->template()->canSkip() && !$step->survey()->isLastStep()){
            $next_step_url = sprintf("%s%s/skip-step",Controller::curr()->Link(), $step->template()->title());

            if( $step->survey() instanceof EntitySurvey){
                $dyn_step_holder = $step->survey()->owner()->template()->title();
                $id              = $step->survey()->getIdentifier();
                $next_step_url   = sprintf("%s%s/edit/%s/skip-step",Controller::curr()->Link(), $dyn_step_holder, $id);
            }

            $fields->add(
                new LiteralField
                (
                    'skip',
                    sprintf
                    (
                        '<p><strong>If you do not wish to answer these questions, you may <a href="%s">skip to the next section</a>.</strong></p>'
                        , $next_step_url
                    )
                )
            );
        }

        if(!empty($content) || $step->template()->canSkip())
            $fields->add(new LiteralField('hr', '<hr/>'));

        foreach ($step->template()->getQuestions() as $q) {
            if ($q->isHidden()) continue;
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

        list($default_action, $actions) = $this->buildActions($action, $step);

        $form = $this->buildForm($form_name, $fields, $actions, $step, $validator);

        $form->setDefaultAction($default_action);
        $form->setAttribute('class','survey_step_form');
        return $form;
    }

    protected function buildForm($form_name, $fields, $actions, $step, $validator){
        return  new RegularStepForm(Controller::curr(), $form_name, $fields, $actions, $step, $validator);
    }
}