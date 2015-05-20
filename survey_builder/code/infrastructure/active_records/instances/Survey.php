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
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Steps')->toArray();
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
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Owner')->getTarget();
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
}