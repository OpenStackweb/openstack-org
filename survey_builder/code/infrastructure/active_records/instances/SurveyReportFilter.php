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
 * Class SurveyReportFilter
 */
class SurveyReportFilter extends DataObject {

    static $db = array
    (
        'Name'  => 'VarChar(255)',
        'Label' => 'VarChar(255)',
        'Order' => 'Int',
    );

    static $has_one = array(
        'Question' => 'SurveyQuestionTemplate',
        'Report'   => 'SurveyReport'
    );

    private static $summary_fields = array(
        'Order'           => 'Order',
        'Name'            => 'Name',
        'Label'           => 'Label',
        'Question.Label'  => 'Question'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $valid_steps = $this->Report()->Template()->Steps()->column();
        $questionList = SurveyMultiValueQuestionTemplate::get()->filter(array('StepID'=> $valid_steps ))->sort('Label')->map('ID','Label')->toArray();
        $questionSelect = DropdownField::create('QuestionID', 'Question')->setSource($questionList);

        $fields->replaceField('QuestionID', $questionSelect);

        return $fields;
    }

}