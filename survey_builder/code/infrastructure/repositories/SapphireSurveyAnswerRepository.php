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
        $template = $q->Step()->SurveyTemplate();
        $filter_query = $this->getFiltersQuery($filters,$template,'s');

        $answers_query = "  SELECT a.`Value` FROM SurveyAnswer AS a
                            LEFT JOIN SurveyStep AS step ON step.ID = a.StepID";

        if ($template->ClassName == 'EntitySurveyTemplate') {
            $answers_query .= " LEFT JOIN EntitySurvey AS es ON es.ID = step.SurveyID
                               LEFT JOIN Survey AS s ON s.ID = es.ParentID";
        } else {
            $answers_query .= " LEFT JOIN Survey AS s ON s.ID = step.SurveyID";
        }

        $answers_query .= " LEFT JOIN SurveyQuestionValueTemplate AS qval ON a.`Value` = qval.ID
                            LEFT JOIN SurveyTemplate AS st ON s.TemplateID = st.ID
                            WHERE s.IsTest = 0
                            AND a.QuestionID = {$question_id} AND a.`Value` IS NOT NULL {$filter_query}";

        $query_result = DB::query($answers_query);
        $answers = $this->mapAnswers($question_id, $query_result);
        $total_answers = $this->SurveyBuilderSurveyCountByQuestion($question_id, $template, $filters);

        return array('answers' => $answers, 'total' => $total_answers);
    }

    public function getFiltersQuery($filters, $template, $table_prefix) {
        $filter_query = "";
        if ($filters) {
            $filter_query .= "AND EXISTS (SELECT DISTINCT(q0.ID) FROM ";

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
                                       WHERE s.IsTest = 0
                                       AND a.QuestionID = {$filter->id} AND FIND_IN_SET({$filter_val},a.`Value`) > 0 ) AS q".$key;

                $filter_query .= ($key > 0) ? " ON q".($key-1).".ID = q{$key}.ID" : "";

            }
            $filter_query .= " WHERE q0.ID = $table_prefix.ID)";
        }

        if ($template->ClassName == 'EntitySurveyTemplate') {
            $template = $template->Parent();
        }

        $start_date = $template->StartDate;
        $end_date = $template->EndDate;

        $filter_query .= " AND $table_prefix.LastEdited > '".$start_date."' AND $table_prefix.LastEdited < '".$end_date."'";

        return $filter_query;
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

    public function SurveyBuilderSurveyCount($template, $filters)
    {
        $filter_query = $this->getFiltersQuery($filters,$template,'I');

        $query = "SELECT COUNT(I.ID) FROM Survey I";

        if ($template->ClassName == 'EntitySurveyTemplate') {
            $query .= " LEFT JOIN EntitySurvey AS ES ON ES.ParentID = I.ID";
            $query .= " LEFT JOIN SurveyTemplate AS ST ON ES.TemplateID = ST.ID";
        } else {
            $query .= " LEFT JOIN SurveyTemplate AS ST ON I.TemplateID = ST.ID";
        }

        $query .= " WHERE ST.ID = $template->ID AND I.IsTest = 0";
        $query .="  AND EXISTS (
                        SELECT COUNT(A.ID) AS AnsweredMandatoryQuestionCount
                        FROM SurveyAnswer A
                        INNER JOIN SurveyStep STP ON STP.ID = A.StepID
                        INNER JOIN Survey S ON S.ID = STP.SurveyID
                        LEFT JOIN SurveyTemplate AS ST ON S.TemplateID = ST.ID";

        $query .=  ($template->ClassName == 'EntitySurveyTemplate') ? " WHERE S.ID = ES.ID " : " WHERE S.ID = I.ID ";
        $query .= "AND S.IsTest = 0 AND A.QuestionID IN (
                        SELECT Q.ID FROM SurveyQuestionTemplate Q
                        INNER JOIN SurveyStepTemplate STP ON STP.ID = Q.StepID AND STP.SurveyTemplateID = $template->ID
                        WHERE Q.Mandatory = 1
                        AND NOT EXISTS (
                            SELECT ID FROM SurveyQuestionTemplate_DependsOn DP WHERE SurveyQuestionTemplateID = Q.ID
                        )
                    )
                    GROUP BY S.ID
                ){$filter_query};
        ";

        return intval(DB::query($query)->value());
    }

    public function SurveyBuilderSurveyCountByQuestion($question_id, $template, $filters)
    {
        $filter_query = $this->getFiltersQuery($filters,$template,'I');

        $question = $template->getQuestionById($question_id);
        if(is_null($question)) return 0;

        $dependencies = $question->getDependsOn();

        if(count($dependencies) == 0) return $this->SurveyBuilderSurveyCount($template, $filters);
        $dependencies_sql = "
           AND EXISTS
           (
              SELECT COUNT(A.ID) AS DependenciesAnswers
              FROM SurveyAnswer A
              INNER JOIN SurveyStep STP ON STP.ID = A.StepID
              INNER JOIN Survey S ON S.ID = STP.SurveyID
              WHERE ";
        $dependencies_sql .= ($template->ClassName == 'EntitySurveyTemplate') ? "S.ID = ES.ID" : "S.ID = I.ID";
        $dependencies_sql .= " AND S.IsTest = 0 AND ( ";

        $index_dep = 0;
        foreach($dependencies as $dependency){
            $question_id      = $dependency->ID;
            $value_id         = $dependency->ValueID;
            $boolean_operator = $dependency->BooleanOperatorOnValues;
            $dependencies_sql .= sprintf("( A.QuestionID = %s  AND A.Value = '%s' )", $question_id, $value_id);

            if(count($dependencies) - 1 > $index_dep)   $dependencies_sql .= sprintf(" %s ", $boolean_operator);
            ++$index_dep;
        }

        $dependencies_sql .= "  ) GROUP BY S.ID ) ";

        $query = "SELECT COUNT(I.ID) FROM Survey I";

        if ($template->ClassName == 'EntitySurveyTemplate') {
            $query .= " LEFT JOIN EntitySurvey AS ES ON ES.ParentID = I.ID";
            $query .= " LEFT JOIN SurveyTemplate AS ST ON ES.TemplateID = ST.ID";
        } else {
            $query .= " LEFT JOIN SurveyTemplate AS ST ON I.TemplateID = ST.ID";
        }

        $query .= "
            WHERE ST.ID = $template->ID AND I.IsTest = 0
            AND EXISTS
            (
                SELECT COUNT(A.ID) AS AnsweredMandatoryQuestionCount
                FROM SurveyAnswer A
                INNER JOIN SurveyStep STP ON STP.ID = A.StepID
                INNER JOIN Survey S ON S.ID = STP.SurveyID
                WHERE ";
        $query .= ($template->ClassName == 'EntitySurveyTemplate') ? "S.ID = ES.ID" : "S.ID = I.ID";
        $query .= " AND S.IsTest = 0 AND A.QuestionID IN
                (
                    SELECT Q.ID FROM SurveyQuestionTemplate Q
                    INNER JOIN SurveyStepTemplate STP ON STP.ID = Q.StepID AND STP.SurveyTemplateID = $template->ID
                    WHERE Q.Mandatory = 1 AND NOT EXISTS ( SELECT ID FROM SurveyQuestionTemplate_DependsOn DP WHERE SurveyQuestionTemplateID = Q.ID )
                )
                GROUP BY S.ID
            )
            {$dependencies_sql}
            {$filter_query};
        ";

        return intval(DB::query($query)->value());
    }

}