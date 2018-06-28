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
        $skip_questions = ['NetPromoter'];
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
                if ($q->ShowOnSangriaStatistics && !in_array($q->Name,$skip_questions)) {
                    array_push($res, $q);
                }
            }
        }

        return new ArrayList($res);
    }

    public function getQuestionByName($name) {
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (is_null($template)) {
            return null;
        }

        $questions = $template->getAllQuestions();
        foreach($questions as $q) {
            if ($q->Name == $name) return $q;
        }

        if (is_a($template, 'EntitySurveyTemplate')) {
            $questions = $template->Parent()->getAllQuestions();
            foreach($questions as $q) {
                if ($q->Name == $name) return $q;
            }
        }

        return null;
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

            $questions_filters = explode(',', $questions_filters);
            $output = '';
            foreach ($questions_filters as $qid) {
                if (empty($qid)) {
                    continue;
                } else if ($qid == 'lang') {
                    $output .= 'Language,';
                } else if ($qid == 'nps') {
                    $output .= 'NPS,';
                }
                $q = SurveyQuestionTemplate::get()->byID($qid);
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

        //die('temp: '.$template_id);

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
     * @param bool $matchClass
     * @return array|null|Session|string
     */
    private function generateFilters($matchClass)
    {
        $request = Controller::curr()->getRequest();
        $from = $request->getVar('From');
        $to = $request->getVar('To');
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();

        $filters = Session::get
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

        $survey_compare = 'I.ID';
        if ($matchClass == 'EntitySurveyTemplate') {
            $survey_compare = 'EI.ParentID';
        }

        $lang_query = <<<SQL
        AND EXISTS
        (
            SELECT * FROM SurveyAnswer A2
            INNER JOIN SurveyStep S2 ON S2.ID = A2.StepID
            INNER JOIN Survey I2 ON I2.ID = S2.SurveyID
            WHERE I2.IsTest = 0
            AND I2.ID = $survey_compare
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
            WHERE I2.ID = $survey_compare
            AND I2.IsTest = 0 AND A2.QuestionID = %s
            AND SQVT2.`Value` >= %s AND SQVT2.`Value` < %s
        )
SQL;

        $filters_where = '';

        if (!empty($from) && !empty($to)) {
            $filters_where = " AND " . SangriaPage_Controller::generateDateFilters('I', 'LastEdited');
        }

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
                        $survey_compare = 'I2.ID = I.ID';
                    } else {
                        if ($question_template_class == 'EntitySurveyTemplate') {
                            $survey_compare = 'EI2.ParentID = I.ID';
                        } else {
                            $survey_compare = 'I2.ID = EI.ParentID';
                        }
                    }

                    $filter_query_tpl = is_int($vid) ? $filter_query_tpl_int : $filter_query_tpl_str;
                    $filters_where .= sprintf($filter_query_tpl, $survey_compare, $qid, $vid);
                }

            }

        }

        return $filters_where;
    }

    private function generateDependenciesFilter($dependencies) {
        $dependencies_sql = "";
        if (count($dependencies)) {
            $dependencies_sql = <<<SQL
               AND EXISTS
               (
                  SELECT COUNT(A.ID) AS DependenciesAnswers
                  FROM SurveyAnswer A
                  INNER JOIN SurveyStep STP ON STP.ID = A.StepID
                  INNER JOIN Survey S ON S.ID = STP.SurveyID
                  WHERE S.ID = I.ID AND S.IsTest = 0 AND (
SQL;

            $index_dep = 0;
            foreach ($dependencies as $dependency) {
                $dep_question_id = $dependency->ID;
                $value_id = $dependency->ValueID;
                $boolean_operator = $dependency->BooleanOperatorOnValues;
                $dependencies_sql .= sprintf("( A.QuestionID = %s  AND A.Value = '%s' )", $dep_question_id, $value_id);

                if (count($dependencies) - 1 > $index_dep) $dependencies_sql .= sprintf(" %s ", $boolean_operator);
                ++$index_dep;
            }

            $dependencies_sql .= "  ) GROUP BY S.ID ) ";
        }

        return $dependencies_sql;
    }

    private function generateMandatoryAnswersFilter() {
        $filter = <<<SQL
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
                INNER JOIN SurveyStepTemplate STP ON STP.ID = Q.StepID
                WHERE Q.Mandatory = 1 AND NOT EXISTS ( SELECT ID FROM SurveyQuestionTemplate_DependsOn DP WHERE SurveyQuestionTemplateID = Q.ID )
            )
            GROUP BY S.ID
        )
SQL;

        return $filter;
    }

    public function SurveyBuilderSurveyCount()
    {
        if ($this->total_count > 0) return $this->total_count;

        $template = $this->getCurrentSelectedSurveyTemplate();

        if (is_null($template)) return 0;

        $class_name = $this->getCurrentSelectedSurveyClassName();

        $filters_where = $this->generateFilters($template->ClassName);
        $mandatory_filter = $this->generateMandatoryAnswersFilter();

        $query = <<<SQL
    SELECT COUNT(I.ID) FROM Survey I
    LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
    WHERE
    I.TemplateID = $template->ID AND I.ClassName = '{$class_name}' AND I.IsTest = 0
    {$mandatory_filter}
    {$filters_where};
SQL;

        $this->total_count = intval(DB::query($query)->value());

        return $this->total_count;
    }

    private $matrix_count_by_question = array();

    public function SurveyBuilderSurveyCountByQuestion($question_id)
    {

        if (isset($this->matrix_count_by_question[$question_id])) return $this->matrix_count_by_question[$question_id];

        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();

        if (is_null($template)) return 0;

        $question = $template->getQuestionById($question_id);
        if (is_null($question)) return 0;

        $dependencies = $question->getDependsOn();
        $dependencies_sql = $this->generateDependenciesFilter($dependencies);
        $mandatory_filter = $this->generateMandatoryAnswersFilter();

        $filters_where = $this->generateFilters($question->Step()->SurveyTemplate()->ClassName);

        $query = <<<SQL
    SELECT COUNT(I.ID) FROM Survey I
    LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
    WHERE I.TemplateID = $template->ID AND I.ClassName = '{$class_name}' AND I.IsTest = 0
    AND EXISTS
    (
        SELECT A.ID AS Answers
        FROM SurveyAnswer A
        INNER JOIN SurveyStep STP ON STP.ID = A.StepID
        INNER JOIN Survey S ON S.ID = STP.SurveyID
        WHERE S.ID = I.ID AND S.IsTest = 0 AND A.QuestionID = $question_id AND A.`Value` IS NOT NULL
    )
    {$filters_where}
    {$mandatory_filter};
SQL;

        $this->matrix_count_by_question[$question_id] = intval(DB::query($query)->value());
        return $this->matrix_count_by_question[$question_id];
    }

    public function SurveyBuilderDeploymentCompanyList($back_url = '')
    {
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();

        if (is_null($template)) return 0;

        $join_survey_entity = '';
        if ($class_name == 'EntitySurvey') {
            $question = $template->Parent()->getAllFilterableQuestions()->filter('ClassName', 'SurveyOrganizationQuestionTemplate')->first();
            $template_id = $template->ParentID;
            $join_survey_entity = 'INNER JOIN EntitySurvey EI ON EI.ParentID = I.ID';
        } else {
            $question = $template->getAllFilterableQuestions()->filter('ClassName', 'SurveyOrganizationQuestionTemplate')->first();
            $template_id = $template->ID;
        }

        $filters_where = $this->generateFilters('SurveyTemplate');
        $mandatory_filter = $this->generateMandatoryAnswersFilter();

        $query = "
        SELECT A.`Value` AS Company, COUNT(I.ID) AS SurveyCount, I.ID AS ID
        FROM Survey AS I
        LEFT JOIN SurveyStep AS SSTEP ON SSTEP.SurveyID = I.ID
        LEFT JOIN SurveyAnswer AS A ON A.StepID = SSTEP.ID
        LEFT JOIN SurveyQuestionTemplate AS Q ON Q.ID = A.QuestionID
        $join_survey_entity
        WHERE I.TemplateID = $template_id AND I.IsTest = 0
        AND Q.ClassName = 'SurveyOrganizationQuestionTemplate'
        {$mandatory_filter}
        {$filters_where} 
        GROUP BY A.`Value`, I.ID
        ORDER BY A.`Value`;";

        $companies = new ArrayList();
        foreach (DB::query($query) as $company_row) {
            $link = '';
            if ($company_row['SurveyCount'] == 1) {
                $link = 'sangria/SurveyDetails/' . $company_row['ID'] . '?BackUrl=' . $back_url;
            } else if ($company_row['SurveyCount'] > 1) {
                $link = 'sangria/SurveyBuilderListSurveys?survey_template_id=' . $template_id . '&question_id=' . $question->ID . '&question_value=' . $company_row['Company'];
            }

            $companies->push(
                new ArrayData(
                    array(
                        'Company' => $company_row['Company'],
                        'Link' => $link
                    )
                )
            );
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
            return "Surveys Submitted (w/ at least 1 mandatory answer)";
        } else {
            return "Deployments Submitted (w/ at least 1 mandatory answer)";
        }
    }

    private function ViewStatisticsSurveyBuilder(SS_HTTPRequest $request, $action, $class_name)
    {
        Requirements::javascript('themes/openstack/javascript/sangria/sangria.page.view.statistics.surveybuilder.js');
        Requirements::css('sangria/ui/source/css/sangria.css');
        Requirements::css('themes/openstack/css/sangria/sangria.page.view.statistics.surveybuilder.css');

        $qid = $request->requestVar('qid');
        $vid = $request->requestVar('vid');
        $clear_filters = $request->requestVar('clear_filters');
        $from = $request->requestVar('From');
        $to = $request->requestVar('To');
        $template_id = intval($request->requestVar('survey_template_id'));

        if (empty($template_id)) {
            if (!$template_id = Session::get(sprintf("SurveyBuilder.%sStatistics.TemplateId", $class_name))) {
                $template = $this->SurveyBuilderSurveyTemplates($class_name)->last();
                $template_id = $template->ID;
            }
        } else {
            Session::clear(sprintf("SurveyBuilder.%sStatistics.Filters", $class_name));
            Session::clear(sprintf("SurveyBuilder.%sStatistics.Filters_Questions", $class_name));
        }

        Session::set(sprintf("SurveyBuilder.%sStatistics.TemplateId", $class_name), $template_id);

        if (!empty($clear_filters)) {
            Session::clear(sprintf("SurveyBuilder.%sStatistics.Filters", $class_name));
            Session::clear(sprintf("SurveyBuilder.%sStatistics.Filters_Questions", $class_name));

            return Controller::curr()->redirect(Controller::curr()->Link($action));
        }
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

        if (!empty($qid) && !empty($vid)) {
            $qid = is_int($qid) ? intval($qid) : $qid;
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

        JQPlotDependencies::renderRequirements();

        Requirements::javascript('sangria/code/js/survey.deployment.stat.builder.js');


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

    private $matrix_count = array();

    /**
     * @param $question_id
     * @param $row_id
     * @param $column_id
     * @return string
     */
    public function SurveyBuilderMatrixCountAnswers($question_id, $row_id, $column_id)
    {
        $key = $question_id . '.' . $row_id . '.' . $column_id;

        if (isset($this->matrix_count[$key])) return $this->matrix_count[$key];

        $class_name = $this->getCurrentSelectedSurveyClassName();
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (is_null($template)) return;

        $question = $template->getQuestionById($question_id);
        if (is_null($question)) return 0;


        $filters_where = $this->generateFilters($question->Step()->SurveyTemplate()->ClassName);

        $query = <<<SQL
        SELECT COUNT(A.Value) FROM SurveyAnswer A
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
        INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
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

    private $matrix_dont_answered = array();

    private function getDontAnsweredCount($question_id)
    {
        if (isset($this->matrix_dont_answered[$question_id])) return $this->matrix_dont_answered[$question_id];

        $template = $this->getCurrentSelectedSurveyTemplate();
        $question = $template->getQuestionById($question_id);
        $filter_where = $this->generateFilters($question->Step()->SurveyTemplate()->ClassName);
        $dependencies = $question->getDependsOn();

        if (count($dependencies) == 0 && $question->isMandatory() && !$question->isHidden()) return 0;

        if (count($dependencies) == 0) {
            // total of survey that answered this question
            $query = <<<SQL
        SELECT COUNT(ID) FROM
        (
            SELECT I.ID FROM SurveyAnswer A
            INNER JOIN SurveyStep STP ON STP.ID = A.StepID
            INNER JOIN Survey I ON I.ID = STP.SurveyID
            LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
            WHERE
            I.TemplateID = {$template->ID} AND I.IsTest = 0 AND
            NOT EXISTS
            (
                SELECT A1.ID FROM SurveyAnswer A1
                INNER JOIN SurveyStep STP1 ON STP1.ID = A1.StepID
                INNER JOIN Survey S1 ON S1.ID = STP1.SurveyID WHERE A1.QuestionID = {$question_id} AND S1.ID = I.ID
            )
            $filter_where
            GROUP BY I.ID
        ) DONT_ANSWERED_QUESTION_N;
SQL;
        } else {

            $dependencies_sql = <<<SQL
       AND EXISTS
       (
          SELECT COUNT(A.ID) AS DependenciesAnswers
          FROM SurveyAnswer A
          INNER JOIN SurveyStep STP ON STP.ID = A.StepID
          INNER JOIN Survey S2 ON S2.ID = STP.SurveyID
          WHERE
          S2.ID = I.ID 
          AND I.IsTest = 0 AND (
SQL;

            $index_dep = 0;
            foreach ($dependencies as $dependency) {
                $question_id = $dependency->ID;
                $visibility = $dependency->Visibility;
                $operator = $dependency->Operator;
                $value_id = $dependency->ValueID;
                $boolean_operator = $dependency->BooleanOperatorOnValues;
                $dependencies_sql .= sprintf("( A.QuestionID = %s  AND A.Value = '%s' )", $question_id, $value_id);

                if (count($dependencies) - 1 > $index_dep) $dependencies_sql .= sprintf(" %s ", $boolean_operator);
                ++$index_dep;
            }
            $dependencies_sql .= " )  GROUP BY S2.ID ) ";

            $query = <<<SQL
        SELECT COUNT(ID) FROM
        (
            SELECT I.ID FROM SurveyAnswer A
            INNER JOIN SurveyStep STP ON STP.ID = A.StepID
            INNER JOIN Survey I ON I.ID = STP.SurveyID
            LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
            WHERE
            I.TemplateID = {$template->ID} AND I.IsTest = 0 AND
            NOT EXISTS
            (
                SELECT A1.ID FROM SurveyAnswer A1
                INNER JOIN SurveyStep STP1 ON STP1.ID = A1.StepID
                INNER JOIN Survey S1 ON S1.ID = STP1.SurveyID WHERE A1.QuestionID = {$question_id} AND S1.ID = I.ID
            )
            {$dependencies_sql}
            {$filter_where}
            GROUP BY I.ID
        ) DONT_ANSWERED_QUESTION_N;
SQL;
        }

        $this->matrix_dont_answered[$question_id] = intval(DB::query($query)->value());

        return $this->matrix_dont_answered[$question_id];
    }

    public function SurveyBuilderMatrixPercentAnswers($question_id, $row_id, $column_id)
    {
        $count = $this->SurveyBuilderMatrixCountAnswers($question_id, $row_id, $column_id);
        $total_count = $this->SurveyBuilderSurveyCountByQuestion($question_id);
        $count_dont_answers = $this->getDontAnsweredCount($question_id);
        $div = $total_count - $count_dont_answers;
        $percent = ($div == 0) ? 0 : ($count / ($div)) * 100;
        $percent = sprintf("%.2f", $percent);

        return $percent . '%';
    }

    public function SurveyBuilderCountAnswers($question_id, $value_id)
    {
        $question_id = intval($question_id);
        $value_id = intval($value_id) > 0 ? intval($value_id) : $value_id;
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();

        if (is_null($template)) return 0;

        $question = $template->getQuestionById($question_id);
        if (is_null($question)) return 0;

        $filters_where = $this->generateFilters($question->Step()->SurveyTemplate()->ClassName);
        $mandatory_filter = $this->generateMandatoryAnswersFilter();

        $query_str = <<<SQL
        SELECT COUNT(A.Value) FROM SurveyAnswer A
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
        INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
        WHERE
        I.TemplateID = $template->ID AND I.IsTest = 0
        AND FIND_IN_SET('{$value_id}', A.Value) > 0
        AND Q.ID = {$question_id}
        {$filters_where}
        {$mandatory_filter};
SQL;

        $query_int = <<<SQL
        SELECT COUNT(A.Value) FROM SurveyAnswer A
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
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
        $qid = intval($request->requestVar('qid'));
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (is_null($template)) return $this->owner->httpError(404, "not found");
        $question = $template->getQuestionById($qid);
        if (is_null($question)) return $this->owner->httpError(404, "not found");
        if (!$question instanceof IDoubleEntryTableQuestionTemplate) return $this->owner->httpError(403, "not instance");

        $results_array = array(array($question->label()));
        $column_labels = array(' ');

        foreach ($question->getColumns() as $column) {
            $column_labels[] = $column->label();
        }
        $results_array[] = $column_labels;

        foreach ($question->getRows() as $row) {
            $rows_array = array($row->label());
            foreach ($row->Columns() as $row_column) {
                $rows_array[] = $this->SurveyBuilderMatrixCountAnswers($qid, $row->ID, $row_column->ID);
            }
            $results_array[] = $rows_array;
        }

        return CSVExporter::getInstance()->export('export_table.csv', $results_array, ',');
    }

    public function getProjectsUsedCombined()
    {

        $template_id = Session::get("SurveyBuilder.EntitySurveyTemplateStatistics.TemplateId");
        $filters_where = $this->generateFilters('EntitySurveyTemplate');

        if (!$template_id) return;

        $pu_questions_query = " SELECT QT.ID FROM SurveyQuestionTemplate QT
                                LEFT JOIN SurveyStepTemplate ST ON ST.ID = QT.StepID
                                WHERE ST.SurveyTemplateID = {$template_id} AND (QT.Name = 'ProjectsUsed' OR QT.Name = 'ProjectsUsedPoC')";

        $pu_question_ids = DB::query($pu_questions_query)->column();

        $answers_query = "  SELECT ANS.`Value` FROM SurveyAnswer ANS
                            INNER JOIN SurveyStep STP ON STP.ID = ANS.StepID
                            INNER JOIN Survey I ON I.ID = STP.SurveyID
                            LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
                            WHERE I.IsTest = 0 AND ANS.QuestionID IN (" . implode(',', $pu_question_ids) . ")
                            AND ANS.`Value` IS NOT NULL {$filters_where}";

        $answers = DB::query($answers_query);

        $question_values = SurveyQuestionValueTemplate::get()
            ->where("OwnerID IN (" . implode(',', $pu_question_ids) . ")")
            ->map('ID', 'Value')->toArray();

        // set question labels
        $values = array();
        $row_values_array = array();
        $total_answers = 0;

        foreach ($pu_question_ids as $pu_question_id) {
            $pu_question = SurveyRadioButtonMatrixTemplateQuestion::get_by_id('SurveyRadioButtonMatrixTemplateQuestion', $pu_question_id);
            foreach ($pu_question->Rows() as $row_value) {
                $row_values_array[$row_value->Value] = 0;
            }
            foreach ($pu_question->Columns() as $col_value) {
                $values[$col_value->Value] = $row_values_array;
            }

            // calculate total answers
            $total_answers += $this->SurveyBuilderSurveyCountByQuestion($pu_question_id);
        }

        // count answers
        foreach ($answers as $answer) {
            $multi_answer = explode(',', $answer['Value']);
            foreach ($multi_answer as $single_answer) {
                if (!$single_answer) continue;

                $matrix = explode(':', $single_answer);
                if (count($matrix) < 2) continue;

                $col = $matrix[0];
                $row = $matrix[1];
                if (!$col || !$row) continue;

                if (!isset($question_values[$col]) || !isset($question_values[$row])) {
                    //print_r($question_values);
                    //die('col: '.$col.' - row: '.$row);
                    continue;
                }

                $row_value = $question_values[$row];
                $col_value = $question_values[$col];
                $values[$row_value][$col_value]++;
            }
        }

        // normalize array - all 3 (prod,test,deploy) must have the same answer options for the graph to render
        // the largest one will have all the answers
        $counts = array_map('count', $values);
        $count_key = array_flip($counts)[max($counts)];
        $largest_arr = $values[$count_key];
        $all_values = array_keys($largest_arr);

        foreach ($values as $key => $val) {
            foreach ($all_values as $key2) {
                if (!isset($values[$key][$key2])) $values[$key][$key2] = 0;
                $values[$key][$key2] = round(($values[$key][$key2] / $total_answers) * 100);
            }
        }

        return json_encode($values);
    }

    public function getProjectsUsedCombinedCount()
    {
        $template_id = Session::get("SurveyBuilder.EntitySurveyTemplateStatistics.TemplateId");
        if (!$template_id)
            return 0;

        $pu_questions_query = " SELECT QT.ID FROM SurveyQuestionTemplate QT
                                LEFT JOIN SurveyStepTemplate ST ON ST.ID = QT.StepID
                                WHERE ST.SurveyTemplateID = {$template_id} AND (QT.Name = 'ProjectsUsed' OR QT.Name = 'ProjectsUsedPoC')";

        $pu_question_ids = DB::query($pu_questions_query)->column();

        $total_answers = 0;

        foreach ($pu_question_ids as $pu_question_id) {
            $total_answers += $this->SurveyBuilderSurveyCountByQuestion($pu_question_id);
        }

        return $total_answers;
    }

    public function getLanguageValues()
    {
        $lang_list = new ArrayList();
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (is_null($template)) {
            return new ArrayList();
        }

        $languages = GroupedList::create($template->Instances())->groupBy('Lang');

        foreach (array_keys($languages) as $lang) {
            if ($lang)
                $lang_list->push(new ArrayData(['Lang' => $lang]));
        }


        return $lang_list;

    }

    public function SurveyBuilderCountLang($lang)
    {
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();
        if (is_null($template)) return;

        $template_id = $template->ID;

        $filters_where = $this->generateFilters('SurveyTemplate');

        // if the question is from survey and the template is entity we need to match ids correctly
        $join_survey_entity = '';
        if ($class_name == 'EntitySurvey') {
            $template_id = $template->Parent()->ID;
            $join_survey_entity = 'INNER JOIN EntitySurvey EI ON EI.ParentID = I.ID';
        }

        $lang_filter = ($lang) ? "AND I.Lang = '{$lang}'" : "AND I.Lang IS NOT NULL";

        $query = <<<SQL
        SELECT COUNT(DISTINCT I.ID) FROM SurveyAnswer A
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
        INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        $join_survey_entity
        WHERE I.IsTest = 0
        {$lang_filter} AND SSTPL.ID = $template_id
        {$filters_where};
SQL;

        return DB::query($query)->value();
    }

    public function SurveyBuilderSurveyNPS($question_id)
    {
        $class_name = $this->getCurrentSelectedSurveyClassName();
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (is_null($template)) return 0;
        $template_id = $template->ID;

        // if the question is from survey and the template is entity we need to match ids correctly
        $join_survey_entity = '';
        if ($class_name == 'EntitySurvey') {
            $question = $template->Parent()->getQuestionById($question_id);
            $template_id = $template->Parent()->ID;
            $join_survey_entity = 'INNER JOIN EntitySurvey EI ON EI.ParentID = I.ID';
        } else {
            $question = $template->getQuestionById($question_id);
        }

        if (is_null($question)) return 0;

        $filters_where = $this->generateFilters('SurveyTemplate');
        $mandatory_filter = $this->generateMandatoryAnswersFilter();

        $query = <<<SQL
    SELECT SUM(IF(V.`Value` < 7, 1, 0)) AS D, SUM(IF(V.`Value` > 6 AND V.`Value` < 9, 1, 0)) AS N, SUM(IF(V.`Value` > 8, 1, 0)) AS P
    FROM SurveyAnswer A
    INNER JOIN SurveyQuestionValueTemplate V ON V.ID = A.`Value`
    LEFT JOIN SurveyStep S ON S.ID = A.StepID
    LEFT JOIN Survey I ON I.ID = S.SurveyID
    $join_survey_entity
    WHERE A.QuestionID = $question_id AND A.`Value` IS NOT NULL AND
    I.TemplateID = $template_id AND I.IsTest = 0
    {$mandatory_filter}
    {$filters_where};
SQL;

        $results = new ArrayList();
        $total_count = $this->SurveyBuilderSurveyCountNPS($question_id);

        if ($total_count) {
            foreach(DB::query($query) as $row) {
                $d = round($row['D']/$total_count,2)*100;
                $n = round($row['N']/$total_count,2)*100;
                $p = round($row['P']/$total_count,2)*100;

                $results->push(new ArrayData(['Label' => 'D', 'Value' => $d]));
                $results->push(new ArrayData(['Label' => 'N', 'Value' => $n]));
                $results->push(new ArrayData(['Label' => 'P', 'Value' => $p]));
                $results->push(new ArrayData(['Label' => 'NPS', 'Value' => ($p - $d)]));

            }
        }

        return $results;
    }

    public function SurveyBuilderSurveyCountNPS($question_id)
    {
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();
        if (is_null($template)) return 0;
        $template_id = $template->ID;


        // if the question is from survey and the template is entity we need to match ids correctly
        $join_survey_entity = '';
        if ($class_name == 'EntitySurvey') {
            $template_id = $template->Parent()->ID;
            $question = $template->Parent()->getQuestionById($question_id);
            $join_survey_entity = 'INNER JOIN EntitySurvey EI ON EI.ParentID = I.ID';
        } else {
            $question = $template->getQuestionById($question_id);
        }

        if (is_null($question)) return 0;

        $dependencies = $question->getDependsOn();
        $dependencies_sql = $this->generateDependenciesFilter($dependencies);

        $filters_where = $this->generateFilters('SurveyTemplate');
        $mandatory_filter = $this->generateMandatoryAnswersFilter();

        $query = <<<SQL
    SELECT COUNT(DISTINCT I.ID) FROM Survey I
    $join_survey_entity
    WHERE I.TemplateID = $template_id AND I.IsTest = 0
    AND EXISTS
    (
        SELECT A.ID AS Answers FROM SurveyAnswer A
        INNER JOIN SurveyStep STP ON STP.ID = A.StepID
        INNER JOIN Survey S ON S.ID = STP.SurveyID
        WHERE S.ID = I.ID AND S.IsTest = 0 AND A.QuestionID = $question_id AND A.`Value` IS NOT NULL
    )
    {$filters_where}
    {$mandatory_filter};
SQL;

        //echo $query .'<br><br><br><br>';
        return intval(DB::query($query)->value());
    }


    public function SurveyBuilderCountNPSAnswers($question_id, $value_id)
    {
        $question_id = intval($question_id);
        $value_id = intval($value_id) > 0 ? intval($value_id) : $value_id;
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();
        if (is_null($template)) return 0;
        $template_id = $template->ID;

        // if the question is from survey and the template is entity we need to match ids correctly
        $join_survey_entity = '';
        if ($class_name == 'EntitySurvey') {
            $question = $template->Parent()->getQuestionById($question_id);
            $template_id = $template->Parent()->ID;
            $join_survey_entity = 'INNER JOIN EntitySurvey EI ON EI.ParentID = I.ID';
        } else {
            $question = $template->getQuestionById($question_id);
        }

        if (is_null($question)) return 0;

        $filters_where = $this->generateFilters('SurveyTemplate');
        $mandatory_filter = $this->generateMandatoryAnswersFilter();

        $query = <<<SQL
        SELECT COUNT(DISTINCT I.ID) FROM SurveyAnswer A
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        $join_survey_entity
        WHERE
        I.TemplateID = $template_id AND I.IsTest = 0
        AND Q.ID = $question_id AND A.`Value` = {$value_id}
        {$filters_where}
        {$mandatory_filter};
SQL;

        //echo $query .'<br><br><br><br>';
        return DB::query($query)->value();
    }

}