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
        'Description' => 'HTMLText',
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
            $extra_label = '';

            $answers = $repository->getByQuestionAndFilters($graph->Question()->ID, $filters);
            $total_answers = $answers['total'];
            $answers = $answers['answers'];

            if ($graph->Question()->ClassName == 'SurveyRadioButtonMatrixTemplateQuestion') {
                //fill up template
                $row_values_array = array();
                foreach ($graph->Question()->Rows() as $row_value) {
                    $row_values_array[$row_value->Value] = 0;
                }
                foreach ($graph->Question()->Columns() as $col_value) {
                    $values[$col_value->Value] = $row_values_array;
                }
            }

            foreach ($answers as $answer) {
                if (!$answer) continue;

                if ($graph->Question()->ClassName == 'SurveyRadioButtonMatrixTemplateQuestion') {
                    $col = $answer->col;
                    $row = $answer->row;
                    $values[$row][$col]++;
                } else {
                    if (!isset($values[$answer]))
                        $values[$answer] = 0;

                    $values[$answer]++;
                }
            }

            if ($graph->Question()->Name == 'NetPromoter') {
                $promoter_perc = round(($values['Promoter'] / $total_answers) * 100);
                $detractor_perc = round(($values['Detractor'] / $total_answers) * 100);
                $extra_label = 'NPS: '.($promoter_perc - $detractor_perc);
            }

            //sort results
            //arsort($values);

            // hide answers if less than 10
            if ($total_answers <= 10) {
                $values = array();
                $total_answers = 0;
            }

            if($graph->Type == 'pie' && count($values) > 10) {
                $other_values = array_slice($values,13);
                $values = array_slice($values,0,12);
                $values['Other'] = 0;
                foreach ($other_values as $val) {
                    $values['Other'] += $val;
                }
            }

            // all bar graphs are shown as percentage
            if($graph->Type == 'bars') {
                foreach ($values as $key => $val) {
                    $values[$key] = round(($val / $total_answers) * 100);
                }
            }

            $questions[] = array(
                'ID'         => $graph->Question()->ID,
                'Graph'      => $graph->Type,
                'Title'      => $graph->Label,
                'Values'     => $values,
                'Total'      => $total_answers,
                'ExtraLabel' => $extra_label,
            );
        }

        $section_map['Questions'] = $questions;

        return $section_map;

    }

}