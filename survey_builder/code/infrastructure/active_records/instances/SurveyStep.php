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
 * Class SurveyStep
 */
class SurveyStep
    extends DataObject
    implements ISurveyStep {

    static $db = array(
    );

    static $indexes = array(
    );

    static $has_one = array(
        'Template' => 'SurveyStepTemplate',
        'Survey'   => 'Survey',
    );

    static $many_many = array(
    );

    static $has_many = array(
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
     * @return ISurveyStepTemplate
     */
    public function template()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Template')->getTarget();
    }

    /**
     * @return ISurvey
     */
    public function survey()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Survey')->getTarget();
    }

    /**
     * @return bool
     */
    public function canSkip()
    {
        if($this->template()->canSkip())
            return true;
        if($this instanceof ISurveyDynamicEntityStep && count($this->getEntitySurveys()) > 0 ){
            return true;
        }
        return false;
    }

    /**
     * @throws AbstractMethodException
     */
    public function clear()
    {
        throw new AbstractMethodException('getAnswers');
    }

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
    }
}