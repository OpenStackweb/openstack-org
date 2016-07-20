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
 * Class SapphireAnswerSurveyRepository
 */
class SapphireAnswerSurveyRepository
    extends SapphireRepository
    implements ISurveyAnswerRepository {

    public function __construct(){
        parent::__construct(new SurveyAnswer());
    }

    /**
     * @param int $question_id
     * @param array $filters
     * @return ArrayList
     */
    public function getByQuestionAndFilters($question_id, $filters = null)
    {
        $q = SurveyQuestionTemplate::get_by_id('SurveyQuestionTemplate',$question_id);

        $answers_query = "  SELECT a.`Value` FROM SurveyAnswer AS a
                            LEFT JOIN SurveyStep AS step ON step.ID = a.StepID";

        if ($q->Step()->SurveyTemplate()->ClassName == 'EntitySurveyTemplate') {
            $answers_query .= " LEFT JOIN EntitySurvey AS es ON es.ID = step.SurveyID
                               LEFT JOIN Survey AS s ON s.ID = es.ParentID";
        } else {
            $answers_query .= " LEFT JOIN Survey AS s ON s.ID = step.SurveyID";
        }

        $answers_query .= " LEFT JOIN SurveyQuestionValueTemplate AS qval ON a.`Value` = qval.ID
                            LEFT JOIN SurveyTemplate AS st ON s.TemplateID = st.ID
                            WHERE s.IsTest = 0 AND (s.Created BETWEEN st.StartDate AND st.EndDate )
                            AND a.QuestionID = {$question_id} AND a.`Value` IS NOT NULL";

        if ($filters) {
            $filter_query = "SELECT DISTINCT(q0.ID) FROM ";

            foreach($filters as $key => $filter) {
                $filter_q = SurveyQuestionTemplate::get_by_id('SurveyQuestionTemplate',$filter->id);
                $filter_val = (is_string($filter->value)) ? "'".$filter->value."'" : $filter->value;

                $filter_query .= ($key > 0) ? " INNER JOIN " : "";
                $filter_query .= "(SELECT s.ID FROM Survey AS s ";

                if ($filter_q->Step()->SurveyTemplate()->ClassName == 'EntitySurveyTemplate') {
                    $filter_query .= " LEFT JOIN EntitySurvey AS es ON es.ParentID = s.ID
                                          LEFT JOIN SurveyStep AS step ON step.SurveyID = es.ID";
                } else {
                    $filter_query .= " LEFT JOIN SurveyStep AS step ON step.SurveyID = s.ID";
                }

                $filter_query .= " LEFT JOIN SurveyAnswer AS a ON a.StepID = step.ID
                                   LEFT JOIN SurveyQuestionValueTemplate AS qval ON a.`Value` = qval.ID
                                   LEFT JOIN SurveyTemplate AS st ON s.TemplateID = st.ID
                                   WHERE s.IsTest = 0 AND (s.Created BETWEEN st.StartDate AND st.EndDate )
                                   AND a.QuestionID = {$filter->id} AND FIND_IN_SET({$filter_val},a.`Value`) > 0 ) AS q".$key;

                $filter_query .= ($key > 0) ? " ON q".($key-1).".ID = q{$key}.ID" : "";
            }

            $answers_query .= " AND s.ID IN ($filter_query)";
        }

        //die($answers_query);

        $query_result = DB::query($answers_query);
        $answers = $this->mapAnswers($question_id, $query_result);
        $total_answers = $query_result->numRecords();

        return array('answers' => $answers, 'total' => $total_answers);
    }

    /**
     * @param int $question_id
     * @param ArrayList $answers
     * @return ArrayList
     */
    public function mapAnswers($question_id, $answers) {
        $answer_values = new ArrayList();
        $question_values = SurveyQuestionValueTemplate::get()->where('OwnerID = '.$question_id)->map('ID','Value')->toArray();
        $question = SurveyQuestionTemplate::get_by_id('SurveyQuestionTemplate',$question_id);

        foreach($answers as $answer) {
            $multi_answer = explode(',',$answer['Value']);
            foreach($multi_answer as $single_answer) {
                if (!$single_answer) continue;

                if ($question->ClassName == 'SurveyRadioButtonMatrixTemplateQuestion') {
                    $matrix = explode(':',$single_answer);
                    $col = $matrix[0];
                    $row = $matrix[1];
                    if (!$col || !$row) continue;

                    $answer_value = array('col' => $question_values[$col],'row' => $question_values[$row]);
                    $answer_values->push($answer_value);
                } else if ($question->Name == 'NetPromoter') {
                    $answer_value = $question_values[$single_answer];
                    if ($answer_value < 7) {
                        $answer_value = 'Detractor';
                    } else if ($answer_value < 9) {
                        $answer_value = 'Neutral';
                    } else {
                        $answer_value = 'Promoter';
                    }
                    $answer_values->push($answer_value);
                } else if(isset($question_values[$single_answer])){
                    $answer_value = $question_values[$single_answer];
                    $answer_values->push($answer_value);
                } else {
                    $answer_values->push($single_answer);
                }
            }
        }

        return $answer_values;
    }

}