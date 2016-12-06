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
 * Class SurveyReportGraph
 */
class SurveyReportGraph extends DataObject {

    static $db = array
    (
        'Name'  => 'VarChar(255)',
        'Label' => 'HTMLText',
        'Type'  => "Enum('pie,bars,multibars','pie')",
        'Order' => 'Int',
    );

    static $has_one = array(
        'Question' => 'SurveyQuestionTemplate',
        'Section' => 'SurveyReportSection'
    );

    private static $summary_fields = array(
        'Order' => 'Order',
        'Name'  => 'Name',
        'Label' => 'Label',
        'Type'  => 'Graph Type'
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
        $valid_steps = $this->Section()->Report()->Template()->Steps()->column();
        $entity_survey_temp = $this->Section()->Report()->Template()->EntitySurveys();
        foreach ($entity_survey_temp as $est) {
            $est_steps = $est->Steps()->column();
            $valid_steps = array_merge($valid_steps,$est_steps);
        }
        $questionList = SurveyQuestionTemplate::get()->filter(array('StepID'=> $valid_steps ))->sort('Label')->map('ID','Label')->toArray();
        $questionSelect = DropdownField::create('QuestionID', 'Question')->setSource($questionList);

        $fields->replaceField('QuestionID', $questionSelect);
        $fields->replaceField('Label', $label = new HtmlEditorField('Label'));
        $label->setRows(4);

        return $fields;
    }

}