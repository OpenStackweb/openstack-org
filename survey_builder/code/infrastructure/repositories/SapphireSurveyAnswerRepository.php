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
        $answers_query = "  SELECT qval.`Value` FROM SurveyAnswer AS a
                            LEFT JOIN SurveyStep AS step ON step.ID = a.StepID";

        if ($filters) {
            $q = SurveyQuestionTemplate::get_by_id('SurveyQuestionTemplate',$question_id);
            if ($q->Step()->SurveyTemplate()->ClassName == 'EntitySurveyTemplate') {
                $answers_query .= " LEFT JOIN EntitySurvey AS es ON es.ID = step.SurveyID
                                   LEFT JOIN Survey AS s ON s.ID = es.ParentID";
            } else {
                $answers_query .= " LEFT JOIN Survey AS s ON s.ID = step.SurveyID";
            }
        } else {
            $answers_query .= " LEFT JOIN Survey AS s ON s.ID = step.SurveyID";
        }

        $answers_query .= " LEFT JOIN SurveyQuestionValueTemplate AS qval ON a.`Value` = qval.ID
                            WHERE a.QuestionID = {$question_id}";

        if ($filters) {
            $filter_query = "SELECT DISTINCT(q0.ID) FROM ";

            foreach($filters as $key => $filter) {
                $filter_q = SurveyQuestionTemplate::get_by_id('SurveyQuestionTemplate',$filter->id);

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
                                      WHERE a.QuestionID = {$filter->id} AND qval.`Value` = '{$filter->value}' ) AS q".$key;

                $filter_query .= ($key > 0) ? " ON q".($key-1).".ID = q{$key}.ID" : "";
            }

            $answers_query .= " AND s.ID IN ($filter_query)";
        }

        $answers = DB::query($answers_query);

        return $answers;
    }
}