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
class Survey
    extends DataObject
    implements ISurvey {

    static $db = array(
        'BeenEmailed' => 'Boolean',
    );

    static $indexes = array(

    );

    static $has_one = array(
        'Template'       => 'SurveyTemplate',
        'CreatedBy'      => 'Member',
        'CurrentStep'    => 'SurveyStep',
        'MaxAllowedStep' => 'SurveyStep',
    );

    static $many_many = array(
    );

    static $has_many = array(
        'Steps' => 'SurveyStep',
    );

    private static $defaults = array(
        'BeenEmailed' => false,
    );

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
        return new ArrayList(AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Steps', $query)->toArray());
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
        $d     = array();

        foreach($steps as $s){
            array_push($d, $s->template()->title());
        }

        $desired_index = array_search($step_name, $d);
        if($desired_index === false) return false;
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
     * @return ISurveyStep
     */
    public function completeCurrentStep(){

        $steps = $this->getSteps();
        $d     = array();

        foreach($steps as $s){
            array_push($d, $s->template()->title());
        }

        $current_step_index = array_search($this->currentStep()->template()->title(), $d);
        if(($current_step_index+1) === count($d)) return $this->currentStep();

        $new_step = $this->getStep($d[$current_step_index+1]);

        $this->registerAllowedMaxStep($new_step);
        $this->registerCurrentStep($new_step);
        return $new_step;
    }


    /**
     * @param string $step_name
     * @return ISurveyStep|null
     */
    public function getStep($step_name){
        foreach($this->getSteps() as $s){
            if($s->template()->title() === $step_name) {
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
    public function sentEmail(IMessageSenderService $service){
        if($this->BeenEmailed) throw new EntityValidationException(array( array('message'=>'Survey Email Already sent !')));
        if(!$this->isLastStep()) throw new EntityValidationException(array( array('message'=>'Survey is not on last step!')));
        $current_step = $this->currentStep();
        if(!$current_step->template() instanceof ISurveyThankYouStepTemplate)  throw new EntityValidationException(array( array('message'=>'Survey last step is not thank you template!')));
        $this->BeenEmailed = true;
        $service->send($this);
    }

    /**
     * @param ISurveyQuestionTemplate $question
     * @return ISurveyAnswer
     */
    public function findAnswerByQuestion(ISurveyQuestionTemplate $question)
    {
        foreach($this->getSteps() as $step){
            if($step instanceof SurveyRegularStep){
                return $step->getAnswerByTemplateId($question->getIdentifier());
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
}