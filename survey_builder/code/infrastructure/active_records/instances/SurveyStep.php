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

    private static $db = [
        'State' => "Enum('INCOMPLETE,COMPLETE','INCOMPLETE')",
    ];

    private static $indexes = [
    ];

    private static $has_one = [
        'Template' => 'SurveyStepTemplate',
        'Survey'   => 'Survey',
    ];

    private static $many_many =  [
    ];

    private static $has_many = [
    ];

    private static $defaults =  [
    ];

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
        return $this->getComponent('Template');
    }

    /**
     * @param ISurveyStepTemplate $template
     */
    public function setTemplate(ISurveyStepTemplate $template){
        $this->TemplateID = $template->getIdentifier();
    }

    /**
     * @return ISurvey
     */
    public function survey()
    {
        return $this->getComponent('Survey');
    }

    /**
     * @param ISurvey $survey
     */
    public function setSurvey(ISurvey $survey){
        $this->SurveyID = $survey->getIdentifier();
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
    }

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
    }

    /**
     * @return bool
     */
    public function hasAnswers()
    {
        $template = $this->Template();
        return ($template instanceof ISurveyRegularStepTemplate);
    }

    /**
     * @return bool
     */
    public function containsEntities()
    {
        $template = $this->Template();
        return ($template instanceof ISurveyDynamicEntityStepTemplate);
    }

    /**
     * @return $this
     */
    public function markComplete(){
        $this->State = ISurveyStep::CompleteState;
        return $this;
    }

    /**
     * @return $this
     */
    public function markIncomplete(){
        $this->State = ISurveyStep::IncompleteState;
        return $this;
    }

    /**
     * @return bool
     */
    public function isComplete(){
        return $this->State == ISurveyStep::CompleteState;
    }
}