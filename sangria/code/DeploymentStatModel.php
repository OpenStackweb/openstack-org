<?php

/**
 * Copyright 2018 OpenStack Foundation
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
class DeploymentStatModel
{

    public function getSurveyQuestionsForReport($template)
    {
        $survey_questions = [
            'Industry', 'IndustrySydney', 'ITActivity', 'ITActivitySydney', 'PrimaryCountry',
            'PrimaryCountrySydney', 'OpenStackActivity', 'OpenStackActivitySydney', 'BusinessDrivers',
            'OrgSize', 'OrgSizeSydney', 'ContainerFormat', 'InteractingClouds'
        ];

        $res = array();

        foreach ($template->Parent()->getAllQuestions() as $q) {
            if (in_array($q->Name, $survey_questions)) {
                array_push($res, $q);
            }
        }

        //$res = array_slice($res, 0, 1);

        return new ArrayList($res);
    }

    public function getDeploymentQuestionsForReport($template)
    {
        $skip_questions = ['NetPromoter'];
        $res = array();

        foreach ($template->Steps()->sort('Order') as $step) {
            if (!$step instanceof ISurveyRegularStepTemplate) {
                continue;
            }

            foreach ($step->Questions()->sort('Order') as $q) {
                if ($q->ShowOnSangriaStatistics && !in_array($q->Name, $skip_questions)) {
                    array_push($res, $q);
                }
            }
        }

        //$res = array_slice($res, 0, 1);

        return new ArrayList($res);
    }

    /**
     * @param bool $matchClass
     * @return array|null|Session|string
     */
    public function getQuestionFilters($matchClass, $tablePrefix, $matchID)
    {
        $className = Session::get('SurveyBuilder.Statistics.ClassName');
        $filters = Session::get(sprintf('SurveyBuilder.%sStatistics.Filters', $className));

        // if question is from deployment : I2.ID = I.ID
        // if question is from survey : I2.ID = EI.ParentID

        $filter_query_tpl_int = <<<SQL
        AND EXISTS
        (
            SELECT * FROM SurveyAnswer A2
            INNER JOIN SurveyQuestionTemplate Q2 ON Q2.ID = A2.QuestionID
            INNER JOIN SurveyQuestionValueTemplate V2 ON V2.OwnerID = Q2.ID
            INNER JOIN SurveyStep S2 ON S2.ID = A2.StepID
            INNER JOIN Survey I2 ON I2.ID = S2.SurveyID
            LEFT JOIN EntitySurvey EI2 ON EI2.ID = I2.ID
            WHERE I2.IsTest = 0
            AND FIND_IN_SET(V2.ID, A2.Value) > 0
            AND %s
            AND Q2.ID = %s
            AND V2.ID = %s
        )
SQL;

        $filter_query_tpl_str = <<<SQL
        AND EXISTS
        (
            SELECT * FROM SurveyAnswer A2
            INNER JOIN SurveyQuestionTemplate Q2 ON Q2.ID = A2.QuestionID
            INNER JOIN SurveyStep S2 ON S2.ID = A2.StepID
            INNER JOIN Survey I2 ON I2.ID = S2.SurveyID
            LEFT JOIN EntitySurvey EI2 ON EI2.ID = I2.ID
            WHERE I2.IsTest = 0
            AND %s
            AND Q2.ID = %s
            AND FIND_IN_SET('%s', A2.Value) > 0
        )
SQL;

        if ($matchClass == 'SurveyTemplate') {
            $survey_compare = "I2.ID = {$tablePrefix}.{$matchID}";
        } else {
            $survey_compare = "I2.ID = E{$tablePrefix}.ParentID";
        }

        $lang_query = <<<SQL
        AND EXISTS
        (
            SELECT * FROM SurveyAnswer A2
            INNER JOIN SurveyStep S2 ON S2.ID = A2.StepID
            INNER JOIN Survey I2 ON I2.ID = S2.SurveyID
            LEFT JOIN EntitySurvey EI2 ON EI2.ID = I2.ID
            WHERE I2.IsTest = 0
            AND {$survey_compare}
            AND I2.Lang = '%s'
        )
SQL;

        $nps_query = <<<SQL
        AND EXISTS
        (
            SELECT * FROM SurveyAnswer A2
            INNER JOIN SurveyStep S2 ON S2.ID = A2.StepID
            INNER JOIN Survey I2 ON I2.ID = S2.SurveyID
            INNER JOIN SurveyQuestionValueTemplate SQVT2 ON SQVT2.ID = A2.`Value`
            WHERE {$survey_compare}
            AND I2.IsTest = 0 AND A2.QuestionID = %s
            AND SQVT2.`Value` >= %s AND SQVT2.`Value` < %s
        )
SQL;

        $filters_where = '';

        if (!empty($filters)) {
            $filters = trim($filters, ',');
            $filters = explode(',', $filters);
            foreach ($filters as $t) {
                $t = explode(':', $t);
                $qid = is_int($t[0]) ? intval($t[0]) : $t[0];
                $vid = is_int($t[1]) ? intval($t[1]) : $t[1];

                if ($qid == 'lang') {
                    $filters_where .= sprintf($lang_query, $vid);
                } else if($qid == 'nps') {
                    $qid = $t[1];
                    $vid = $t[2];
                    if ($vid == 'D') {
                        $lower = 0;
                        $upper = 7;
                    } else if ($vid == 'N') {
                        $lower = 7;
                        $upper = 9;
                    } else { //vid = P
                        $lower = 9;
                        $upper = 11;
                    }
                    $filters_where .= sprintf($nps_query, $qid, $lower, $upper);
                } else {
                    if (count($t) === 3)
                        $vid = sprintf('%s:%s', $t[1], $t[2]);

                    $question_template_class = SurveyQuestionTemplate::get()->byID($qid)->Step()->SurveyTemplate()->ClassName;

                    if ($question_template_class == $matchClass) {
                        $survey_compare = "I2.ID = {$tablePrefix}.{$matchID}";
                    } else if ($question_template_class == 'EntitySurveyTemplate') {
                        $survey_compare = "EI2.ParentID = {$tablePrefix}.{$matchID}";
                    } else {
                        $survey_compare = "I2.ID = E{$tablePrefix}.ParentID";
                    }

                    $filter_query_tpl = is_int($vid) ? $filter_query_tpl_int : $filter_query_tpl_str;
                    $filters_where .= sprintf($filter_query_tpl, $survey_compare, $qid, $vid);
                }

            }

        }

        return $filters_where;
    }

    public function getMandatoryAnswersFilter($matchClass) {

        $compareId = 'ID';

        if ($matchClass == 'SurveyTemplate') {
            $compareId = 'ParentID';
        }

        $filter = <<<SQL
        AND EXISTS
        (
            SELECT A.ID AS MandatoryAnswerId
            FROM SurveyAnswer A
            INNER JOIN SurveyStep STP ON STP.ID = A.StepID
            INNER JOIN EntitySurvey ES ON ES.ID = STP.SurveyID
            INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
            WHERE ES.{$compareId} = I.ID AND Q.Mandatory = 1
        )
SQL;

        return $filter;
    }

}