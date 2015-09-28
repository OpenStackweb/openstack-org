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

class SurveyAnswer extends DataObject implements ISurveyAnswer
{

    static $db = array
    (
        'Value' => 'Text',
    );

    static $indexes = array(

    );

    static $has_one = array(
        'Question' => 'SurveyQuestionTemplate',
        'Step'     => 'SurveyStep',
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
     * @return string
     */
    public function value()
    {
        return $this->getField('Value');
    }

    /**
     * @return ISurveyQuestionTemplate
     */
    public function question()
    {
       return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Question')->getTarget();
    }

    /**
     * @return ISurveyStep
     */
    public function step()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Step')->getTarget();
    }

    /**
     * @return string
     */
    public function getFormattedAnswer()
    {
        $res = $this->Value;
        $question = $this->Question();
        if($question instanceof SurveyMultiValueQuestionTemplate)
        {
            $res = explode(',', $res);
            $aux = '';
            foreach($res as $v){
                if($question instanceof SurveyDoubleEntryTableQuestionTemplate)
                {
                    $tuple = explode(':', $v);
                    $value1 = $question->getRowById(intval($tuple[0]));
                    $value2 = $question->getColumnById(intval($tuple[1]));

                    $aux .= sprintf("%s (%s),", $value1->label(), $value2->label());
                }
                else {
                    $value = $question->getValueById(intval($v));
                    if (is_null($value)) {
                        continue;
                    }
                    $aux .= $value->label() . ',';
                }
            }
            $res = trim($aux, ',');
        }
        return $res;
    }
}