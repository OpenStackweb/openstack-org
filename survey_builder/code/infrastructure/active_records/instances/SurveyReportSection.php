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

            foreach ($answers as $answer) {
                if (!$answer) continue;

                // NPS mapping
                if ($graph->Question()->Name == 'NetPromoter') {
                    if ($answer < 7) {
                        $answer = 'Detractor';
                    } else if ($answer < 9) {
                        $answer = 'Neutral';
                    } else {
                        $answer = 'Promoter';
                    }


                }
                // end NPS mapping
                if (!isset($values[$answer]))
                    $values[$answer] = 0;

                $values[$answer]++;
            }

            if ($graph->Question()->Name == 'NetPromoter') {
                $promoter_perc = round(($values['Promoter'] / $total_answers) * 100);
                $detractor_perc = round(($values['Detractor'] / $total_answers) * 100);
                $extra_label = 'NPS: '.($promoter_perc - $detractor_perc).'%';
            }

            //sort results
            arsort($values);

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