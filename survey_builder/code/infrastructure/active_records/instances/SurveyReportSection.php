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
 * Class SurveyReportSection
 */
class SurveyReportSection extends DataObject {

    static $db = array
    (
        'Name'  => 'VarChar(255)',
        'Order' => 'Int',
        'Description' => 'HtmlText',
    );

    static $has_one = array
    (
        'Report' => 'SurveyReport',
    );

    static $has_many = array (
        'Graphs' => 'SurveyReportGraph',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }



    public function mapSection($filters)
    {
        $section_map = $this->toMap();
        $repository = new SapphireAnswerSurveyRepository();
        $questions = array();

        foreach ($this->Graphs()->sort('Order') as $graph) {
            $values = array();
            $total = 0;

            $answers = $repository->getByQuestionAndFilters($graph->Question()->ID, $filters);

            foreach ($answers as $answer) {
                $answer_text = $answer['Value'];
                if (!isset($values[$answer_text]))
                    $values[$answer_text] = 0;

                $values[$answer_text]++;
                $total++;
            }

            $questions[] = array(
                'ID'         => $graph->Question()->ID,
                'Graph'      => $graph->Type,
                'Title'      => $graph->Label,
                'Values'     => $values,
                'Total'      => $total,
            );
        }

        $section_map['Questions'] = $questions;

        return $section_map;

    }

}