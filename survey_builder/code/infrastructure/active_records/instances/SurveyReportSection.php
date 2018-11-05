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
        $repository  = new SapphireAnswerSurveyRepository();
        $questions   = [];

        // some questions have dependant questions that we need to show
        $expand_questions = ['NetworkDrivers'];

        foreach ($this->Graphs()->sort('Order') as $graph) {
            $values      = [];
            $extra_label = '';
            $question    = $graph->Question();

            $answers       = [];
            if ($question->is_a('SurveyDropDownQuestionTemplate') && $question->isCountrySelector()) {
                $answers       = $repository->getContinentAnswers($question->ID, $filters);
            } else {
                $answers       = $repository->getByQuestionAndFilters($question->ID, $filters);
            }

            $total_answers = $answers['total'];
            $answers       = $answers['answers'];

            if (in_array($question->Name, $expand_questions)) {
                foreach ($question->getDependers() as $depender_question) {
                    if ($depender_question->is_a('SurveyCheckBoxListQuestionTemplate')) {
                        $dep_answers   = $repository->getByQuestionAndFilters($depender_question->ID, $filters);
                        $answers->merge($dep_answers['answers']);
                    }
                }
            }

            // set labels for multibars
            if ($graph->Type == 'multibars') {
                //fill up template
                $row_values_array = array();
                foreach ($question->Rows() as $row_value) {
                    $row_values_array[$row_value->Value] = 0;
                }
                foreach ($question->Columns() as $col_value) {
                    $values[$col_value->Value] = $row_values_array;
                }
            } else {
                if ($question->Name == 'NetPromoter') {
                    $values = array('Detractor' => 0, 'Neutral' => 0, 'Promoter' => 0);
                } else if ($question->is_a('SurveyDropDownQuestionTemplate') && $question->isCountrySelector()) {
                    $values = array_combine(Continent::get()->column('Name'), [0,0,0,0,0,0,0,0]);
                    $values['Prefer not to say'] = 0;
                } else {
                    foreach ($question->getValues() as $value_temp) {
                        $values[$value_temp->Value] = 0;
                    }

                    if (in_array($question->Name, $expand_questions)) {
                        foreach ($question->getDependers() as $depender_question) {
                            if ($depender_question->is_a('SurveyCheckBoxListQuestionTemplate')) {
                                // remove the value that will be expanded from the answers
                                unset($values[$depender_question->ValueID]);
                                // add expanded values
                                foreach ($depender_question->Values() as $dep_value_temp) {
                                    if (in_array($dep_value_temp->Value, $answers->toArray()))
                                        $values[$dep_value_temp->Value] = 0;
                                }
                            }
                        }
                    }

                }
            }


            if (count($answers)) {
                foreach ($answers as $answer) {
                    if (!$answer) continue;

                    if ($graph->Type == 'multibars') {
                        $col = $answer->col;
                        $row = $answer->row;
                        if (!isset($values[$row]) || !isset($values[$row][$col])) continue;
                        $values[$row][$col]++;
                    } else {
                        if (!isset($values[$answer])) continue;
                        $values[$answer]++;
                    }
                }

                // hide answers if less than 10
                if ($total_answers < 10) {
                    $values = array();
                    $total_answers = 0;
                } else {
                    if ($graph->Type == 'multibars') {
                        // show as percentage
                        foreach ($values as $key => $val) {
                            foreach ($val as $key2 => $val2) {
                                $values[$key][$key2] = round(($val2 / $total_answers) * 100);
                            }
                        }
                    } else if ($graph->Type == 'bars') {
                        // show as percentage
                        foreach ($values as $key => $val) {
                            $values[$key] = round(($val / $total_answers) * 100);
                        }
                    }  else if ($graph->Type == 'pie') {
                        // extra label for net promoter
                        if ($graph->Question()->Name == 'NetPromoter') {
                            $promoter_perc = round(($values['Promoter'] / $total_answers) * 100);
                            $detractor_perc = round(($values['Detractor'] / $total_answers) * 100);
                            $extra_label = 'NPS: '.($promoter_perc - $detractor_perc);
                        }

                        arsort($values);

                        // group lower values into 'Other' tag
                        if(count($values) > 10) {
                            $other_values = array_slice($values,13);
                            $values = array_slice($values,0,12);
                            $values['Other'] = 0;
                            foreach ($other_values as $val) {
                                $values['Other'] += $val;
                            }
                        }
                    }
                }
            }

            // remove empty continents
            if ($question->is_a('SurveyDropDownQuestionTemplate') && $question->isCountrySelector()) {
                $values = array_filter($values);
            }

            $questions[] = [
                'ID'         => $graph->Question()->ID,
                'Graph'      => $graph->Type,
                'Title'      => $graph->Label,
                'Values'     => $values,
                'Total'      => $total_answers,
                'ExtraLabel' => $extra_label,
            ];
        }

        $section_map['Questions'] = $questions;

        return $section_map;

    }

}