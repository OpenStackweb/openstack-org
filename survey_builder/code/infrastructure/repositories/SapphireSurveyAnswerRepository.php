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
        $filter_query = $this->getFiltersQuery($filters,$q);

        $answers_query = "  SELECT ANS.`Value` FROM SurveyAnswer ANS
                            LEFT JOIN SurveyStep STP ON STP.ID = ANS.StepID";

        if ($template->ClassName == 'EntitySurveyTemplate') {
            $answers_query .= " LEFT JOIN EntitySurvey ES ON ES.ID = STP.SurveyID
                               LEFT JOIN Survey S ON S.ID = ES.ParentID";
        } else {
            $answers_query .= " LEFT JOIN Survey S ON S.ID = STP.SurveyID";
        }

        $answers_query .= " WHERE S.IsTest = 0 AND ANS.QuestionID = {$question_id} AND ANS.`Value` IS NOT NULL ";
        $answers_query .= $filter_query;

        $query_result = DB::query($answers_query);
        $answers = $this->mapAnswers($question_id, $query_result);
        $total_answers = $this->SurveyBuilderSurveyCountByQuestion($q, $filters);

        //die($answers_query);

        return array('answers' => $answers, 'total' => $total_answers);
    }

    public function getFiltersQuery($filters, $question) {
        $filter_query = "";
        $q_survey_template = $question->Step()->SurveyTemplate();
        $survey_template = ($q_survey_template->ClassName == 'EntitySurveyTemplate') ? $q_survey_template->Parent() : $q_survey_template;

        if ($filters) {
            foreach($filters as $filter) {
                $filter_q = SurveyQuestionTemplate::get_by_id('SurveyQuestionTemplate',$filter->id);
                $filter_template = $filter_q->Step()->SurveyTemplate();

                if ($q_survey_template->ClassName == 'SurveyTemplate' || $filter_template == 'SurveyTemplate') {
                    $table_join = "FS.ID = S.ID";
                } else {
                    $table_join = "FES.ID = ES.ID";
                }

                $filter_query .= "
                    AND EXISTS (
                    SELECT FANS.ID AS FilterAnswers
                    FROM SurveyAnswer FANS
                    INNER JOIN SurveyStep FSTP ON FSTP.ID = FANS.StepID";
                if ($filter_template->ClassName == 'EntitySurveyTemplate') {
                    $filter_query .= "
                    INNER JOIN EntitySurvey FES ON FES.ID = FSTP.SurveyID
                    INNER JOIN Survey FS ON FS.ID = FES.ParentID";
                } else {
                    $filter_query .= "
                    INNER JOIN Survey FS ON FS.ID = FSTP.SurveyID";
                }
                $filter_query .= "
                    WHERE {$table_join} AND FS.IsTest = 0
                    AND FANS.QuestionID = {$filter->id}
                    AND FIND_IN_SET('{$filter->value}',FANS.`Value`) > 0)";
            }
        }

        $start_date = $survey_template->StartDate;
        $end_date = $survey_template->EndDate;

        $filter_query .= " AND S.LastEdited > '".$start_date."' AND S.LastEdited < '".$end_date."'";

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

    public function SurveyBuilderSurveyCountByQuestion($question, $filters)
    {
        $q_survey_template = $question->Step()->SurveyTemplate();
        $question_id = $question->ID;
        $table_join = ($q_survey_template->ClassName == 'EntitySurveyTemplate') ? "ES.ID" : "S.ID";

        $filter_query = $this->getFiltersQuery($filters,$question);

        $dependencies = $question->getDependsOn();

        $dependencies_sql = "";
        if(count($dependencies)) {
            $dependencies_sql = "
               AND EXISTS (
                  SELECT DA.ID
                  FROM SurveyAnswer DA
                  INNER JOIN SurveyStep DSTP ON DSTP.ID = DA.StepID
                  INNER JOIN Survey DS ON DS.ID = DSTP.SurveyID
                  WHERE DS.ID = {$table_join} AND DS.IsTest = 0 AND ( ";

            $index_dep = 0;
            foreach($dependencies as $dependency){
                $question_id      = $dependency->ID;
                $value_id         = $dependency->ValueID;
                $boolean_operator = $dependency->BooleanOperatorOnValues;
                $dependencies_sql .= sprintf("( DA.QuestionID = %s  AND DA.Value = '%s' )", $question_id, $value_id);

                if(count($dependencies) - 1 > $index_dep)   $dependencies_sql .= sprintf(" %s ", $boolean_operator);
                ++$index_dep;
            }

            $dependencies_sql .= "  ) ) ";
        }

        $query = "
            SELECT COUNT(S.ID) FROM Survey S";

        if ($q_survey_template->ClassName == 'EntitySurveyTemplate') {
            $query .= "
            LEFT JOIN EntitySurvey AS ES ON ES.ParentID = S.ID
            LEFT JOIN SurveyTemplate AS ST ON ES.TemplateID = ST.ID";
        } else {
            $query .= "
            LEFT JOIN SurveyTemplate AS ST ON S.TemplateID = ST.ID";
        }

        $query .= "
            WHERE ST.ID = $q_survey_template->ID AND S.IsTest = 0
            AND EXISTS (
                SELECT COUNT(MANS.ID) AS AnsweredMandatoryQuestionCount
                FROM SurveyAnswer MANS
                INNER JOIN SurveyStep MSTP ON MSTP.ID = MANS.StepID
                INNER JOIN Survey MS ON MS.ID = MSTP.SurveyID
                WHERE MS.ID = {$table_join} AND MS.IsTest = 0
                AND MANS.QuestionID IN
                (
                    SELECT MQ.ID FROM SurveyQuestionTemplate MQ
                    INNER JOIN SurveyStepTemplate MQSTP ON MQSTP.ID = MQ.StepID AND MQSTP.SurveyTemplateID = $q_survey_template->ID
                    WHERE MQ.Mandatory = 1
                    AND NOT EXISTS ( SELECT ID FROM SurveyQuestionTemplate_DependsOn DP WHERE SurveyQuestionTemplateID = MQ.ID )
                )
                GROUP BY MS.ID
            ) AND EXISTS (
                SELECT CA.ID
                FROM SurveyAnswer CA
                INNER JOIN SurveyStep CSTP ON CSTP.ID = CA.StepID
                INNER JOIN Survey CS ON CS.ID = CSTP.SurveyID
                WHERE CS.ID = {$table_join} AND CS.IsTest = 0 AND CA.QuestionID = $question_id AND CA.`Value` IS NOT NULL
            )
            {$dependencies_sql}
            {$filter_query};
        ";

        return intval(DB::query($query)->value());
    }

}