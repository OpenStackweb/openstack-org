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
class SangriaPageSurveyBuilderStatisticsExtension extends Extension
{
    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', array(
            'ViewDeploymentStatisticsSurveyBuilder',
            'ViewSurveysStatisticsSurveyBuilder',
            'exportQuestion',
        ));

        Config::inst()->update(get_class($this->owner), 'allowed_actions', array(
            'ViewDeploymentStatisticsSurveyBuilder',
            'ViewSurveysStatisticsSurveyBuilder',
            'exportQuestion',
        ));
    }

    public function SurveyBuilderSurveyTemplates($class_name = 'EntitySurveyTemplate')
    {
        Session::set('SurveyBuilder.Statistics.ClassName', $class_name);
        if ($class_name === 'SurveyTemplate') {
            return SurveyTemplate::get()->filter('ClassName', 'SurveyTemplate');
        }

        return EntitySurveyTemplate::get();
    }

    public function getSurveyQuestions2Show()
    {
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (is_null($template)) {
            return new ArrayList();
        }
        $res = array();
        foreach ($template->Steps()->sort('Order') as $step) {
            if (!$step instanceof ISurveyRegularStepTemplate) {
                continue;
            }

            foreach ($step->Questions()->sort('Order') as $q) {
                if ($q->ShowOnSangriaStatistics) {
                    array_push($res, $q);
                }
            }
        }

        return new ArrayList($res);
    }

    /**
     * @return SurveyTemplate|null
     */
    private function getCurrentSelectedSurveyTemplate()
    {

        $template_id = Session::get
        (
            sprintf
            (
                "SurveyBuilder.%sStatistics.TemplateId",
                Session::get('SurveyBuilder.Statistics.ClassName')
            )
        );

        $template = null;
        if (!empty($template_id)) {
            $template = SurveyTemplate::get()->byID(intval($template_id));
            if (!is_null($template) && $template->ClassName === 'EntitySurveyTemplate') {
                $template = EntitySurveyTemplate::get()->byID(intval($template_id));
            }
        }

        return $template;
    }

    /**
     * @return null|string
     */
    private function getCurrentSelectedSurveyClassName()
    {
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (is_null($template)) {
            return null;
        }
        if ($template instanceof EntitySurveyTemplate) {
            return 'EntitySurvey';
        }

        return "Survey";
    }

    public function RenderCurrentFilters()
    {
        $questions_filters = Session::get(sprintf('SurveyBuilder.%sStatistics.Filters_Questions',
            Session::get('SurveyBuilder.Statistics.ClassName')));
        if (!empty($questions_filters)) {
            $template = $this->getCurrentSelectedSurveyTemplate();
            if (is_null($template)) {
                return;
            }

            $questions_filters = explode(',', $questions_filters);
            $output = '';
            foreach ($questions_filters as $qid) {
                if (empty($qid)) {
                    continue;
                }
                $q = $template->getQuestionById($qid);
                if (is_null($q)) {
                    continue;
                }
                $output .= $q->Name . ',';
            }

            return trim($output, ',');
        }

        return '';
    }

    public function IsSurveyTemplateSelected($current_template_id)
    {
        $template_id = Session::get(sprintf("SurveyBuilder.%sStatistics.TemplateId",
            Session::get('SurveyBuilder.Statistics.ClassName')));

        return (!empty($template_id) && !empty($current_template_id) && intval($current_template_id) === intval($template_id));
    }

    public function SurveyBuilderDateFilterQueryString()
    {
        $request = Controller::curr()->getRequest();
        $from = $request->getVar('From');
        $to = $request->getVar('To');
        $query_str = '';
        if (!empty($from) && !empty($to)) {
            $query_str = sprintf("&From=%s&To=%s", $from, $to);
        }

        return $query_str;
    }

    public function IsQuestionOnFiltering($qid)
    {
        $questions_filters = Session::get(sprintf('SurveyBuilder.%sStatistics.Filters_Questions',
            Session::get('SurveyBuilder.Statistics.ClassName')));
        if (!empty($questions_filters)) {
            $questions_filters = explode(',', $questions_filters);

            return in_array($qid, $questions_filters);
        }

        return false;
    }

    private $total_count = 0;

    /**
     * @param string $survey_table_prefix
     * @return array|null|Session|string
     */
    private function generateFilters($survey_table_prefix = 'I')
    {
        $request    = Controller::curr()->getRequest();
        $from       = $request->getVar('From');
        $to         = $request->getVar('To');
        $template   = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();

        $filters    = Session::get
        (
            sprintf
            (
                'SurveyBuilder.%sStatistics.Filters',
                Session::get
                (
                    'SurveyBuilder.Statistics.ClassName'
                )
            )
        );

        $filter_query_tpl_int = <<<SQL
        AND EXISTS
        (
            SELECT * FROM SurveyAnswer A2
            INNER JOIN SurveyQuestionTemplate Q2 ON Q2.ID = A2.QuestionID
            INNER JOIN SurveyStepTemplate STPL2 ON STPL2.ID = Q2.StepID
            INNER JOIN SurveyTemplate SSTPL2 ON SSTPL2.ID = STPL2.SurveyTemplateID
            INNER JOIN SurveyQuestionValueTemplate V2 ON V2.OwnerID = Q2.ID
            INNER JOIN SurveyStep S2 ON S2.ID = A2.StepID
            INNER JOIN Survey I2 ON I2.ID = S2.SurveyID
            WHERE
            I2.ClassName = '{$class_name}' AND I2.IsTest = 0
            AND FIND_IN_SET(V2.ID, A2.Value) > 0
            AND SSTPL2.ID = %s
            AND Q2.ID = %s
            AND V2.ID = %s
            AND I2.ID = {$survey_table_prefix}.ID
        )
SQL;

        $filter_query_tpl_str = <<<SQL
        AND EXISTS
        (
            SELECT * FROM SurveyAnswer A2
            INNER JOIN SurveyQuestionTemplate Q2 ON Q2.ID = A2.QuestionID
            INNER JOIN SurveyStepTemplate STPL2 ON STPL2.ID = Q2.StepID
            INNER JOIN SurveyTemplate SSTPL2 ON SSTPL2.ID = STPL2.SurveyTemplateID
            INNER JOIN SurveyStep S2 ON S2.ID = A2.StepID
            INNER JOIN Survey I2 ON I2.ID = S2.SurveyID
            WHERE
            I2.ClassName = '{$class_name}' AND I2.IsTest = 0
            AND SSTPL2.ID = %s
            AND Q2.ID = %s
            AND I2.ID = {$survey_table_prefix}.ID
            AND FIND_IN_SET('%s', A2.Value) > 0
        )
SQL;
        $filters_where = '';

        if (!empty($from) && !empty($to)) {
            $filters_where = " AND " . SangriaPage_Controller::generateDateFilters($survey_table_prefix, "LastEdited");
        }

        if (!empty($filters))
        {
            $filters = trim($filters, ',');
            $filters = explode(',', $filters);
            foreach ($filters as $t) {
                $t = explode(':', $t);
                $qid = intval($t[0]);
                $vid = is_int($t[1]) ? intval($t[1]) : $t[1];
                if(count($t) === 3)
                    $vid = sprintf('%s:%s', $t[1], $t[2]);
                $filter_query_tpl = is_int($vid) ? $filter_query_tpl_int : $filter_query_tpl_str;
                $filters_where .= sprintf($filter_query_tpl, $template->ID, $qid, $vid);
            }
        }

        return $filters_where;
    }

    public function SurveyBuilderSurveyCount()
    {
        if($this->total_count > 0 ) return $this->total_count;

        $template = $this->getCurrentSelectedSurveyTemplate();

        if (is_null($template))
        {
            return 0;
        }

        $class_name = $this->getCurrentSelectedSurveyClassName();

        $filters_where = $this->generateFilters();

        $query = <<<SQL
    SELECT COUNT(I.ID) FROM Survey I
    WHERE
    I.TemplateID = $template->ID AND I.ClassName = '{$class_name}' AND I.IsTest = 0
    AND EXISTS
    (
        SELECT COUNT(A.ID) AS AnsweredMandatoryQuestionCount
        FROM SurveyAnswer A
        INNER JOIN SurveyStep STP ON STP.ID = A.StepID
        INNER JOIN Survey S ON S.ID = STP.SurveyID
        WHERE
        S.ID = I.ID AND S.IsTest = 0 AND
        A.QuestionID IN
        (
            SELECT Q.ID FROM SurveyQuestionTemplate Q
            INNER JOIN SurveyStepTemplate STP ON STP.ID = Q.StepID AND STP.SurveyTemplateID = $template->ID
            WHERE Q.Mandatory = 1 AND NOT EXISTS ( SELECT ID FROM SurveyQuestionTemplate_DependsOn DP WHERE SurveyQuestionTemplateID = Q.ID )
        )
        GROUP BY S.ID
    )
    {$filters_where};
SQL;

        $this->total_count = intval(DB::query($query)->value());

        return $this->total_count;
    }

    public function SurveyBuilderDeploymentCompanyList()
    {
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();

        if (is_null($template))
        {
            return 0;
        }

        if ($class_name != 'EntitySurvey') //only works for deployment
        {
            return 0;
        }

        $filters_where = $this->generateFilters();

        $query = <<<SQL
    SELECT SANS.`Value` AS Company FROM Survey AS I
    LEFT JOIN EntitySurvey AS ES ON ES.ID = I.ID
    LEFT JOIN Survey AS I2 ON I2.ID = ES.ParentID
    LEFT JOIN SurveyStep AS SSTEP ON SSTEP.SurveyID = I2.ID
    LEFT JOIN SurveyAnswer AS SANS ON SANS.StepID = SSTEP.ID
    LEFT JOIN SurveyQuestionTemplate AS SQUEST ON SQUEST.ID = SANS.QuestionID
    WHERE
    I.TemplateID = $template->ID AND I.ClassName = '{$class_name}' AND I.IsTest = 0
    AND SQUEST.ClassName = 'SurveyOrganizationQuestionTemplate'
    AND EXISTS
    (
        SELECT COUNT(A.ID) AS AnsweredMandatoryQuestionCount
        FROM SurveyAnswer A
        INNER JOIN SurveyStep STP ON STP.ID = A.StepID
        INNER JOIN Survey S ON S.ID = STP.SurveyID
        WHERE
        S.ID = I.ID AND S.IsTest = 0 AND
        A.QuestionID IN
        (
            SELECT Q.ID FROM SurveyQuestionTemplate Q
            INNER JOIN SurveyStepTemplate STP ON STP.ID = Q.StepID AND STP.SurveyTemplateID = $template->ID
            WHERE Q.Mandatory = 1 AND NOT EXISTS ( SELECT ID FROM SurveyQuestionTemplate_DependsOn DP WHERE SurveyQuestionTemplateID = Q.ID )
        )
        GROUP BY S.ID
    )
    {$filters_where} GROUP BY SANS.`Value` ORDER BY SANS.`Value`;
SQL;

        $companies = new ArrayList();
        foreach (DB::query($query)->column('Company') as $company) {
            $companies->push(new ArrayData(array('Company' => $company)));
        }

        return $companies;
    }

    public function SurveyBuilderLabelSubmitted()
    {
        $class_name = Session::get('SurveyBuilder.Statistics.ClassName');
        if (empty($class_name)) {
            return;
        }
        if ($class_name === 'SurveyTemplate') {
            return "Surveys Submitted";
        } else {
            return "Deployments Submitted";
        }
    }

    private function ViewStatisticsSurveyBuilder(SS_HTTPRequest $request, $action, $class_name)
    {
        Requirements::javascript('themes/openstack/javascript/sangria/sangria.page.view.statistics.surveybuilder.js');
        Requirements::css('themes/openstack/css/sangria/sangria.page.view.statistics.surveybuilder.css');
        $qid           = $request->requestVar('qid');
        $vid           = $request->requestVar('vid');
        $clear_filters = $request->requestVar('clear_filters');
        $from          = $request->requestVar('From');
        $to            = $request->requestVar('To');
        $template_id   = intval($request->requestVar('survey_template_id'));

        if (empty($template_id)) {
            $template = $this->SurveyBuilderSurveyTemplates($class_name)->last();
            $template_id = $template->ID;
        }

        Session::set(sprintf("SurveyBuilder.%sStatistics.TemplateId", $class_name), $template_id);
        Session::clear(sprintf("SurveyBuilder.%sStatistics.Filters", $class_name));
        Session::clear(sprintf("SurveyBuilder.%sStatistics.Filters_Questions", $class_name));

        if (empty($from) || empty($to)) {
            $template = SurveyTemplate::get()->byID(intval($template_id));

            if ($class_name === 'EntitySurveyTemplate') {
                $template = $template->Parent();
            }

            $from = date('Y/m/d H:i', strtotime($template->StartDate));
            $to = date('Y/m/d H:i', strtotime($template->EndDate));
            $query_str = sprintf("?From=%s&To=%s", $from, $to);

            return Controller::curr()->redirect(Controller::curr()->Link($action) . $query_str);
        }

        if (!empty($clear_filters)) {
            Session::clear(sprintf("SurveyBuilder.%sStatistics.Filters", $class_name));
            Session::clear(sprintf("SurveyBuilder.%sStatistics.Filters_Questions", $class_name));

            return Controller::curr()->redirect(Controller::curr()->Link($action));
        } else {
            if (!empty($qid) && !empty($vid)) {
                $qid = intval($qid);
                $vid = is_int($vid) ? intval($vid) : $vid;
                $filters = Session::get(sprintf('SurveyBuilder.%sStatistics.Filters', $class_name));
                $questions_filters = Session::get(sprintf('SurveyBuilder.%sStatistics.Filters_Questions', $class_name));
                $filters .= sprintf("%s:%s,", $qid, $vid);
                $questions_filters .= sprintf("%s,", $qid);

                Session::set(sprintf("SurveyBuilder.%sStatistics.Filters", $class_name), $filters);
                Session::set(sprintf("SurveyBuilder.%sStatistics.Filters_Questions", $class_name), $questions_filters);

                $query_str = '';
                if (!empty($from) && !empty($to)) {
                    $query_str = sprintf("?From=%s&To=%s", $from, $to);
                }

                return Controller::curr()->redirect(Controller::curr()->Link($action) . $query_str);
            }
        }

        return $this->owner->Customise
        (
            array
            (
                'ClassName' => $class_name,
                'Action' => $action
            )
        )->renderWith(array('SangriaPage_ViewStatisticsSurveyBuilder', 'SangriaPage', 'SangriaPage'));
    }

    public function ViewDeploymentStatisticsSurveyBuilder(SS_HTTPRequest $request)
    {
        $this->clearStatisticsSurveyBuilderSessionData('SurveyTemplate');

        return $this->ViewStatisticsSurveyBuilder($request, 'ViewDeploymentStatisticsSurveyBuilder',
            'EntitySurveyTemplate');
    }

    public function ViewSurveysStatisticsSurveyBuilder(SS_HTTPRequest $request)
    {
        $this->clearStatisticsSurveyBuilderSessionData('EntitySurveyTemplate');

        return $this->ViewStatisticsSurveyBuilder($request, 'ViewSurveysStatisticsSurveyBuilder', 'SurveyTemplate');
    }

    private function clearStatisticsSurveyBuilderSessionData($class_name)
    {
        Session::clear(sprintf("SurveyBuilder.%sStatistics.Filters", $class_name));
        Session::clear(sprintf("SurveyBuilder.%sStatistics.Filters_Questions", $class_name));
        Session::clear(sprintf("SurveyBuilder.%sStatistics.TemplateId", $class_name));
    }

    public function IsRegularStep($template)
    {
        return true;
    }

    private $matrix_count  = array();
    /**
     * @param $question_id
     * @param $row_id
     * @param $column_id
     * @return string
     */
    public function SurveyBuilderMatrixCountAnswers($question_id, $row_id, $column_id)
    {
        $key = $question_id.'.'.$row_id.'.'.$column_id;

        if(isset($this->matrix_count[$key])) return $this->matrix_count[$key];

        $template    = $this->getCurrentSelectedSurveyTemplate();

        if (is_null($template))
        {
            return;
        }

        $class_name = $this->getCurrentSelectedSurveyClassName();

        $filters_where = $this->generateFilters();

        $query = <<<SQL
        SELECT COUNT(A.Value) FROM SurveyAnswer A
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
        INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        WHERE
        I.ClassName = '{$class_name}' AND I.IsTest = 0
        AND FIND_IN_SET('{$row_id}:{$column_id}', A.Value) > 0
        AND SSTPL.ID = $template->ID
        AND Q.ID = {$question_id}
        {$filters_where};
SQL;

        $this->matrix_count[$key] = intval(DB::query($query)->value());

        return $this->matrix_count[$key];
    }

    private $matrix_dont_answered  = array();

    private function getDontAnsweredCount($question_id)
    {
        if(isset($this->matrix_dont_answered[$question_id])) return $this->matrix_dont_answered[$question_id];

        $template     = $this->getCurrentSelectedSurveyTemplate();
        $filter_where = $this->generateFilters('S');
        // total of survey that answered this question
        $query = <<<SQL
        SELECT COUNT(ID) FROM
        (
            SELECT S.ID FROM SurveyAnswer A
            INNER JOIN SurveyStep STP ON STP.ID = A.StepID
            INNER JOIN Survey S ON S.ID = STP.SurveyID
            WHERE
            S.TemplateID = {$template->ID} AND S.IsTest = 0 AND
            NOT EXISTS
            (
                SELECT A1.ID FROM SurveyAnswer A1
                INNER JOIN SurveyStep STP1 ON STP1.ID = A1.StepID
                INNER JOIN Survey S1 ON S1.ID = STP1.SurveyID WHERE A1.QuestionID = {$question_id} AND S1.ID = S.ID
            )
            $filter_where
            GROUP BY S.ID
        ) DONT_ANSWERED_QUESTION_N;
SQL;

        $this->matrix_dont_answered[$question_id]   = intval(DB::query($query)->value());

        return $this->matrix_dont_answered[$question_id];
    }

    public function SurveyBuilderMatrixPercentAnswers($question_id, $row_id, $column_id)
    {
        $count              = $this->SurveyBuilderMatrixCountAnswers($question_id, $row_id, $column_id);
        $total_count        = $this->SurveyBuilderSurveyCount();
        $count_dont_answers = $this->getDontAnsweredCount($question_id);
        $div                = $total_count - $count_dont_answers;
        $percent            = ($div == 0) ? 0 : ($count/ ($div )) * 100;
        $percent            = sprintf ("%.2f", $percent);

        return $percent.'%';
    }

    public function SurveyBuilderCountAnswers($question_id, $value_id)
    {
        $question_id = intval($question_id);
        $value_id    = intval($value_id) > 0 ? intval($value_id) : $value_id;
        $template    = $this->getCurrentSelectedSurveyTemplate();

        if (is_null($template))
        {
            return;
        }

        $class_name = $this->getCurrentSelectedSurveyClassName();

        $filters_where  = $this->generateFilters();

        $query_str = <<<SQL
        SELECT COUNT(A.Value) FROM SurveyAnswer A
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
        INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        WHERE
        I.ClassName = '{$class_name}' AND I.IsTest = 0
        AND FIND_IN_SET('{$value_id}', A.Value) > 0
        AND SSTPL.ID = $template->ID
        AND Q.ID = {$question_id}
        {$filters_where};
SQL;

        $query_int = <<<SQL
        SELECT COUNT(A.Value) FROM SurveyAnswer A
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        WHERE
        I.TemplateID =  $template->ID AND I.IsTest = 0
        AND Q.ID = $question_id
        AND EXISTS
        (
            SELECT * FROM SurveyQuestionValueTemplate V
            WHERE V.OwnerID = Q.ID AND FIND_IN_SET(V.ID, A.Value) > 0 AND V.ID = {$value_id}
        )
        {$filters_where};
SQL;
        $query = is_int($value_id) ? $query_int : $query_str;

        return DB::query($query)->value();
    }

    public function exportQuestion(SS_HTTPRequest $request)
    {
        $qid           = intval($request->requestVar('qid'));

        $template = $this->getCurrentSelectedSurveyTemplate();
        $question = $template->getQuestionById($qid);
        $results_array = array(array($question->Label));
        $column_labels = array(' ');

        foreach($question->Columns() as $column) {
            $column_labels[] = $column->Label;
        }
        $results_array[] = $column_labels;

        foreach($question->Rows() as $row) {
            $rows_array = array($row->Label);
            foreach($row->Columns() as $row_column) {
                $rows_array[] = $this->SurveyBuilderMatrixCountAnswers($qid,$row->ID, $row_column->ID);
            }
            $results_array[] = $rows_array;
        }



        return CSVExporter::getInstance()->export('export_table.csv', $results_array, ',');
    }
}