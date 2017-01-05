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
 * Class Survey
 */
class Survey extends DataObject implements ISurvey
{
    const SurveyTestersGroupSlug = 'survey-testers';

    static $db = array
    (
        'BeenEmailed' => 'Boolean',
        'IsTest'      => 'Boolean',
        'State'       => "Enum('INCOMPLETE,SAVED,COMPLETE','INCOMPLETE')",
    );

    static $indexes = array();

    static $has_one = array
    (
        'Template'       => 'SurveyTemplate',
        'CreatedBy'      => 'Member',
        'CurrentStep'    => 'SurveyStep',
        'MaxAllowedStep' => 'SurveyStep',
    );

    static $many_many = array();

    static $has_many = array(
        'Steps' => 'SurveyStep',
    );

    private static $defaults = array(
        'BeenEmailed' => false,
        'IsTest'      => false,
    );


    private static $searchable_fields = array
    (
        'ID',
        'Created',
        'IsTest',
    );

    private static $summary_fields = array
    (
        'ID'                        => 'ID',
        'Created'                   => 'Created',
        'CreatedBy.Email'           => 'CreatedBy',
        'CurrentStep.Template.Name' => 'CurrentStep',
        'EntitiesSurveys.Count'     => '# Deployments',
        'IsTest'                    => 'Is Test ?'
    );

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->CreatedBy()->inGroup(Survey::SurveyTestersGroupSlug)) {
            $this->IsTest = true;
        }
    }

    protected function onAfterWrite()
    {
        parent::onAfterWrite();
        foreach($this->EntitiesSurveys() as $sub_surveys)
        {
              $sub_surveys->IsTest = $this->IsTest;
              $sub_surveys->write();
        }
    }

    public function EntitiesSurveys()
    {
        $res = new ArrayList();
        foreach($this->Steps() as $step)
        {
            if($step instanceof SurveyDynamicEntityStep)
            {
                foreach($step->EntitySurveys() as $sub_surveys)
                {
                    $res->add($sub_surveys);
                }
            }
        }
        return $res;
    }

    /**
     * @return int
     */
    public function EntitySurveysCount(){
        return count($this->EntitiesSurveys());
    }

    /**
     * @param $question_name
     * @return null|string
     */
    public function getAnswerFor($question_name){
        foreach($this->Steps() as $step)
        {
            if(!$step instanceof SurveyRegularStep) continue;

            $answer = $step->getAnswerByName($question_name);

            if(is_null($answer)) continue;

            return $answer->value();
        }
        return null;
    }
    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return ISurveyStep
     */
    public function allowedMaxStep()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'MaxAllowedStep')->getTarget();
    }

    /**
     * @return ISurveyStep
     */
    public function currentStep()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'CurrentStep')->getTarget();
    }

    /**
     * @return ISurveyStep[]
     */
    public function getSteps()
    {
        $query = new QueryObject(new SurveyStep);
        $query->addAlias(QueryAlias::create('Template'));
        $query->addOrder(QueryOrder::asc('Template.Order'));

        $list =  new ArrayList
        (
            AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Steps',$query)->toArray()
        );

        return $list;
    }

    /**
     * @return ISurveyTemplate
     */
    public function template()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Template')->getTarget();
    }

    /**
     * @return ICommunityMember
     */
    public function createdBy()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'CreatedBy')->getTarget();
    }

    /**
     * @param ISurveyStep $max_step
     * @return void
     */
    public function registerAllowedMaxStep(ISurveyStep $max_step)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'MaxAllowedStep')->setTarget($max_step);
    }

    /**
     * @param ISurveyStep $current_step
     * @return void
     */
    public function registerCurrentStep(ISurveyStep $current_step)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'CurrentStep')->setTarget($current_step);
    }

    /**
     * @param ISurveyStep $step
     * @return void
     */
    public function addStep(ISurveyStep $step)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Steps')->add($step);
    }

    /**
     * @param string $step_name
     * @return bool
     */
    public function isAllowedStep($step_name)
    {
        $steps = $this->getSteps();
        $d = array();

        foreach ($steps as $s) {
            array_push($d, $s->template()->title());
        }

        $desired_index = array_search($step_name, $d);
        if ($desired_index === false) {
            return false;
        }
        $max_allowed_index = array_search($this->allowedMaxStep()->template()->title(), $d);

        return $desired_index <= $max_allowed_index;
    }

    /**
     * @return bool
     */
    public function isLastStep()
    {
        $last_step = $this->getSteps()->last();

        return $last_step->getIdentifier() === $this->currentStep()->getIdentifier();
    }

    /**
     * @return int
     */
    public function getCurrentStepIndex()
    {
        return $this->getStepIndex($this->currentStep());
    }

    /**
     * @return int
     */
    public function getCurrentStepIndexNice()
    {
        return intval($this->getStepIndex($this->currentStep()))+1;
    }

    public function getStepIndex(ISurveyStep $step)
    {
        $steps = $this->getSteps();
        $d = array();

        foreach ($steps as $s) {
            array_push($d, $s->template()->title());
        }

        return array_search($step->template()->title(), $d);
    }

    /**
     * @return int
     */
    public function getStepsCount()
    {
        return count($this->getSteps());
    }

    /**
     * @param $idx
     * @return null
     */
    public function getStepByIndex($idx)
    {
        $steps = $this->getSteps()->toArray();
        if (count($steps) < $idx) {
            return null;
        }

        return $steps[$idx];
    }

    /**
     * @return ISurveyStep
     */
    public function completeCurrentStep()
    {

        $current_step_index = $this->getCurrentStepIndex();
        $steps_count        = $this->getStepsCount();

        if (($current_step_index + 1) === $steps_count) {
            return $this->currentStep();
        }

        do {
            $next_step = $this->getStepByIndex(++$current_step_index);
            if ($this->canShowStep($next_step)) {
                break;
            }
        } while ($current_step_index <= $steps_count || $next_step instanceof ISurveyThankYouStepTemplate);

        $this->registerCurrentStep($next_step);

        $current_max_step_index = $this->getStepIndex($this->allowedMaxStep());
        if ($current_step_index >= $current_max_step_index) {
            $this->registerAllowedMaxStep($next_step);
        }

        return $next_step;
    }

    /**
     * @param ISurveyStep $step
     * @return bool
     */
    public function canShowStep(ISurveyStep $step)
    {
        $should_show = true;

        // checks if we have a dependency to show it or not
        $static_rules = array();

        foreach ($step->template()->getDependsOn() as $d) {
            // belongs to another step (former one)
            if (!isset($static_rules[$d->getIdentifier()])) {
                $static_rules[$d->getIdentifier()] = array
                (
                    'question' => $d,
                    'values' => array(),
                    'operator' => $d->Operator,
                    'visibility' => $d->Visibility,
                    'default' => $d->DependantDefaultValue,
                    'boolean_operator' => $d->BooleanOperatorOnValues,
                    'initial_condition' => ($d->BooleanOperatorOnValues === 'And') ? true : false
                );
            }

            array_push($static_rules[$d->getIdentifier()]['values'], $d->ValueID);
        }


        foreach ($static_rules as $id => $info) {
            $q = $info['question'];
            $values = $info['values'];
            $operator = $info['operator'];
            $visibility = $info['visibility'];
            $boolean_operator = $info['boolean_operator'];
            $initial_condition = $info['initial_condition'];

            $answer = $this->findAnswerByQuestion($q);
            if (is_null($answer)) {
                return false;
            }

            //checks the condition
            switch ($operator) {
                case 'Equal': {
                    foreach ($values as $vid) {
                        if ($boolean_operator === 'And') {
                            $initial_condition &= (strpos($answer->value(), $vid) !== false);
                        } else {
                            $initial_condition |= (strpos($answer->value(), $vid) !== false);
                        }
                    }
                }
                    break;
                case 'Not-Equal': {
                    foreach ($values as $vid) {
                        if ($boolean_operator === 'And') {
                            $initial_condition &= (strpos($answer->value(), $vid) === false);
                        } else {
                            $initial_condition |= (strpos($answer->value(), $vid) === false);
                        }
                    }
                }
                    break;
            }

            //visibility
            switch ($visibility) {
                case 'Visible': {
                    if (!$initial_condition) {
                        $should_show = false;
                    }
                }
                    break;
                case 'Not-Visible': {
                    if ($initial_condition) {
                        $should_show = false;
                    }
                }
                    break;
            }
        }

        // also we check that has visible questions
        if($step->template()->ClassName == 'SurveyRegularStepTemplate') {
            $has_visible_question = false;
            foreach($step->template()->Questions() as $question) {
                if (!$question->isHidden()) {
                    $has_visible_question = true;
                    break;
                }
            }

            $should_show = ($has_visible_question) ? $should_show : false;
        }

        return $should_show;
    }


    /**
     * @param string $step_name
     * @return ISurveyStep|null
     */
    public function getStep($step_name)
    {
        foreach ($this->getSteps() as $s) {
            if ($s->template()->title() === $step_name) {
                return $s;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isEmailSent()
    {
        return $this->getField('BeenEmailed');
    }

    /**
     * @param IMessageSenderService $service
     * @throws EntityValidationException
     */
    public function sentEmail(IMessageSenderService $service)
    {
        if ($this->BeenEmailed) {
            throw new EntityValidationException(array(array('message' => 'Survey Email Already sent !')));
        }
        if (!$this->isLastStep()) {
            throw new EntityValidationException(array(array('message' => 'Survey is not on last step!')));
        }
        $current_step = $this->currentStep();
        if (!$current_step->template() instanceof ISurveyThankYouStepTemplate) {
            return;
        }
        $this->BeenEmailed = true;
        $service->send($this);
    }

    /**
     * @param ISurveyQuestionTemplate $question
     * @return ISurveyAnswer
     */
    public function findAnswerByQuestion(ISurveyQuestionTemplate $question)
    {
        foreach ($this->getSteps() as $step) {
            if ($step instanceof SurveyRegularStep) {
                $answer = $step->getAnswerByTemplateId($question->getIdentifier());
                if (!is_null($answer)) {
                    return $answer;
                }
            }
        }

        return null;
    }

    /**
     * @param ISurveyStep $step
     * @return void
     */
    public function removeStep(ISurveyStep $step)
    {
        $step->clear();
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Steps')->remove($step);
    }

    protected function onBeforeDelete()
    {
        parent::onBeforeDelete();
        foreach ($this->Steps() as $step) {
            $step->delete();
        }
    }

    /**
     * @param string $step_name
     * @return ISurveyStep|null
     */
    public function getPreviousStep($step_name)
    {
        $previous_list = [];

        foreach ($this->getSteps() as $step) {
            if ($step->template()->title() === $step_name) {
                break;
            }
            if($this->canShowStep($step))
                $previous_list[] = $step;
        }

        return count($previous_list) > 0 ? end($previous_list) : null;
    }

    /**
     * @param string $step_name
     * @return ISurveyStep|null
     */
    public function getNextStep($step_name){
        $next_list = [];
        $reached   = false;
        foreach ($this->getSteps() as $step) {
            if ($step->template()->title() === $step_name) {
                $reached = true;
            }
            if($reached && $step->template()->title() !== $step_name && $this->canShowStep($step))
                $next_list[] = $step;
        }

        return count($next_list) > 0 ? reset($next_list) : null;
    }

    /**
     * @return bool
     */
    public function isFirstStep()
    {
        $first_step = $this->getSteps()->first();

        return $first_step->getIdentifier() === $this->currentStep()->getIdentifier();
    }

    /**
     * @return bool
     */
    public function isCompleted(){
        return $this->MaxAllowedStep()->Template()->ID === $this->Template()->getLastStep()->ID;
    }

    /**
     * @return ISurveyStep[]
     */
    public function getCompletedSteps()
    {
        $completed_steps = array();
        foreach ($this->getSteps() as $step) {
            if ($this->isAllowedStep($step->template()->title()) && $this->canShowStep($step)) {
                $completed_steps[] = $step;
            }
        }

        return new ArrayList($completed_steps);
    }

    /**
     * @return ISurveyStep[]
     */
    public function getAvailableSteps(){
        $available_steps = [];
        foreach ($this->getSteps() as $step) {
            if ($this->canShowStep($step)) {
                $available_steps[] = $step;
            }
        }
        return new ArrayList($available_steps);
    }

    public function getFriendlyName()
    {
        $owner = $this->CreatedBy();

        return sprintf('%s', $owner->Email);
    }

    public function getCMSFields()
    {

        $fields = new FieldList(
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $fields->addFieldsToTab('Root.Main', new HiddenField('TemplateID', 'TemplateID'));
        $fields->addFieldsToTab('Root.Main', new ReadonlyField('CreatedByEmail','CreatedBy', $this->CreatedBy()->Email));
        $fields->addFieldsToTab('Root.Main', new CheckboxField('IsTest', 'IsTest'));

        return $fields;
    }

    /**
     * @return $this
     */
    public function markComplete(){
        $this->State = ISurvey::CompleteState;
        return $this;
    }

    /**
     * @return bool
     */
    public function isComplete(){
        return $this->State == ISurvey::CompleteState;
    }
}