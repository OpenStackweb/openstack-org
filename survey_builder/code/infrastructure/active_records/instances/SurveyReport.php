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
 * Class SurveyReport
 */
class SurveyReport extends DataObject {

    static $db = array
    (

    );

    static $indexes = array
    (

    );

    static $has_one = array
    (
        'Template' => 'SurveyTemplate',
    );

    static $has_many = array(
        'Sections' => 'SurveyReportSection'
    );

    static $many_many = array(
        'Filters' => 'SurveyQuestionTemplate',
    );

    static $many_many_extraFields = array(
        'Filters' => array(
            'Label' => "Varchar(254)",
        ),
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

    public function mapToArray()
    {
        $report_map = $this->toMap();
        $report_map['Template'] = $this->Template()->toMap();

        $filters = array();
        foreach ($this->Filters() as $filter) {
            $label = $this->Filters()->getExtraData('Label',$filter->ID);
            $options = array();

            foreach ($filter->getValues() as $option) {
                $options[] = $option->Value;
            }

            $filters[] = array(
                'Label'    => $label['Label'],
                'Question' => $filter->ID,
                'Options'  => $options,
            );
        }
        $report_map['Filters'] = $filters;

        $report_map['Sections'] = $this->Sections()->toNestedArray();

        return $report_map;

    }




}