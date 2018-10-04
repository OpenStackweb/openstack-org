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

    private $statModel;
    private $matrix_dont_answered = array();
    private $total_count = 0;
    private $matrix_count = array();
    private $matrix_count_by_question = array();

    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', array(
            'ViewDeploymentStatisticsSurveyBuilder',
            'ViewSurveysStatisticsSurveyBuilder',
            'exportQuestion',
            'ExportStatisticsSurveyBuilder'
        ));

        Config::inst()->update(get_class($this->owner), 'allowed_actions', array(
            'ViewDeploymentStatisticsSurveyBuilder',
            'ViewSurveysStatisticsSurveyBuilder',
            'exportQuestion',
            'ExportStatisticsSurveyBuilder'
        ));
    }

    public function ViewDeploymentStatisticsSurveyBuilder(SS_HTTPRequest $request)
    {
        $this->clearStatisticsSurveyBuilderSessionData('SurveyTemplate');

        JQPlotDependencies::renderRequirements();

        Requirements::javascript('sangria/code/js/survey.deployment.stat.builder.js');

        $this->statModel = new DeploymentStatModel();

        return $this->ViewStatisticsSurveyBuilder($request, 'ViewDeploymentStatisticsSurveyBuilder',
            'EntitySurveyTemplate');
    }

    public function ViewSurveysStatisticsSurveyBuilder(SS_HTTPRequest $request)
    {
        $this->clearStatisticsSurveyBuilderSessionData('EntitySurveyTemplate');

        $this->statModel = new SurveyStatModel();

        return $this->ViewStatisticsSurveyBuilder($request, 'ViewSurveysStatisticsSurveyBuilder', 'SurveyTemplate');
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

        $renderTemplate = 'SangriaPage_ViewStatisticsSurveyBuilder_'.$class_name;

        return $this->owner->Customise
        (
            array
            (
                'ClassName' => $class_name,
                'Action' => $action
            )
        )->renderWith(array($renderTemplate, 'SangriaPage', 'SangriaPage'));
    }

    public function SurveyBuilderSurveyTemplates($class_name = 'EntitySurveyTemplate')
    {
        Session::set('SurveyBuilder.Statistics.ClassName', $class_name);
        if ($class_name === 'SurveyTemplate') {
            return SurveyTemplate::get()->filter('ClassName', 'SurveyTemplate');
        }

        return EntitySurveyTemplate::get();
    }

    public function getSurveyQuestionsForReport()
    {
        $template = $this->getCurrentSelectedSurveyTemplate();

        if (is_null($template)) {
            return new ArrayList();
        }

        return $this->statModel->getSurveyQuestionsForReport($template);
    }

    public function getDeploymentQuestionsForReport()
    {
        $template = $this->getCurrentSelectedSurveyTemplate();

        if (is_null($template)) {
            return new ArrayList();
        }

        return $this->statModel->getDeploymentQuestionsForReport($template);
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

    /**
     * @param bool $matchClass
     * @return array|null|Session|string
     */
    private function generateFilters($matchClass, $tablePrefix = 'I', $matchID = 'ID')
    {
        $request = Controller::curr()->getRequest();
        $from = $request->getVar('From');
        $to = $request->getVar('To');

        $filters_where = '';

        if (!empty($from) && !empty($to)) {
            $filters_where = " AND " . SangriaPage_Controller::generateDateFilters($tablePrefix, 'LastEdited');
        }

        $filters_where .= $this->statModel->getQuestionFilters($matchClass, $tablePrefix, $matchID);

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

    private function generateMandatoryAnswersFilter($question_class) {
        return $this->statModel->getMandatoryAnswersFilter($question_class);
    }

    public function SurveyBuilderSurveyCount()
    {
        if ($this->total_count > 0) return $this->total_count;

        $template = $this->getCurrentSelectedSurveyTemplate();

        if (is_null($template)) return 0;

        $class_name = $this->getCurrentSelectedSurveyClassName();

        $filters_where = $this->generateFilters($template->ClassName);
        $mandatory_filter = $this->generateMandatoryAnswersFilter($class_name);

        $query = <<<SQL
    SELECT COUNT(I.ID) FROM Survey I
    LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
    WHERE
    I.TemplateID = $template->ID AND I.ClassName = '{$class_name}' AND I.IsTest = 0
    {$mandatory_filter}
    {$filters_where};
SQL;

        //die('Q: '.$query);

        $this->total_count = intval(DB::query($query)->value());

        return $this->total_count;
    }

    public function SurveyBuilderSurveyCountByQuestion($question_id)
    {
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();
        if (is_null($template)) return 0;

        $question = SurveyQuestionTemplate::get()->byID($question_id);
        if (is_null($question)) return 0;
        $question_class = $question->Step()->SurveyTemplate()->ClassName;

        if (isset($this->matrix_count_by_question[$template->ID.'-'.$question_id]))
            return $this->matrix_count_by_question[$template->ID.'-'.$question_id];


        //$dependencies = $question->getDependsOn();
        //$dependencies_sql = $this->generateDependenciesFilter($dependencies);
        $mandatory_filter = $this->generateMandatoryAnswersFilter($question_class);

        $filters_where = $this->generateFilters($question_class);

        $query = <<<SQL
        SELECT COUNT(DISTINCT I.ID) FROM SurveyAnswer A
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
        WHERE I.IsTest = 0 AND A.QuestionID = {$question_id}
        {$filters_where}
        {$mandatory_filter};
SQL;

        //if ($question_id == 633) die('Q: '.$query);

        $this->matrix_count_by_question[$template->ID.'-'.$question_id] = intval(DB::query($query)->value());
        return $this->matrix_count_by_question[$template->ID.'-'.$question_id];
    }

    public function SurveyBuilderDeploymentCompanyList($back_url = '')
    {
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();

        if (is_null($template)) return 0;

        if ($class_name == 'EntitySurvey') {
            $question = $template->Parent()->getAllFilterableQuestions()->filter('ClassName', 'SurveyOrganizationQuestionTemplate')->first();
            $template_id = $template->ParentID;
        } else {
            $question = $template->getAllFilterableQuestions()->filter('ClassName', 'SurveyOrganizationQuestionTemplate')->first();
            $template_id = $template->ID;
        }

        $question_class = $question->Step()->SurveyTemplate()->ClassName;
        $filters_where = $this->generateFilters('SurveyTemplate');
        $mandatory_filter = $this->generateMandatoryAnswersFilter($question_class);

        $query = "
        SELECT A.`Value` AS Company, COUNT(DISTINCT I.ID) AS SurveyCount, I.ID AS ID
        FROM Survey AS I
        LEFT JOIN SurveyStep AS SSTEP ON SSTEP.SurveyID = I.ID
        LEFT JOIN SurveyAnswer AS A ON A.StepID = SSTEP.ID
        LEFT JOIN SurveyQuestionTemplate AS Q ON Q.ID = A.QuestionID
        WHERE I.IsTest = 0 AND Q.ID = {$question->ID}
        {$mandatory_filter}
        {$filters_where}
        GROUP BY A.`Value`, I.ID
        ORDER BY A.`Value`;";


        //die('Q: '.$query);

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

    /**
     * @param $question_id
     * @param $row_id
     * @param $column_id
     * @return string
     */
    public function SurveyBuilderMatrixCountAnswers($question_id, $row_id, $column_id)
    {
        $class_name = $this->getCurrentSelectedSurveyClassName();
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (is_null($template)) return;

        $key = $template->ID . '.' . $question_id . '.' . $row_id . '.' . $column_id;
        if (isset($this->matrix_count[$key])) return $this->matrix_count[$key];

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

        $question = SurveyQuestionTemplate::get()->byID($question_id);
        if (is_null($question)) return 0;
        $question_class = $question->Step()->SurveyTemplate()->ClassName;

        $filters_where = $this->generateFilters($question->Step()->SurveyTemplate()->ClassName);
        $mandatory_filter = $this->generateMandatoryAnswersFilter($question_class);

        $query = <<<SQL
        SELECT COUNT(DISTINCT I.ID) FROM SurveyAnswer A
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        LEFT JOIN EntitySurvey EI ON EI.ID = I.ID
        WHERE I.IsTest = 0 AND A.QuestionID = {$question_id} AND FIND_IN_SET('{$value_id}', A.Value) > 0
        {$filters_where}
        {$mandatory_filter};
SQL;

        //if ($question_id == 633) die('Q: '.$query);

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
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();

        if (is_null($template)) return 0;

        if ($class_name == 'EntitySurvey') {
            $template_id = $template->ParentID;
        } else {
            $template_id = $template->ID;
        }

        $filters_where = $this->generateFilters('EntitySurveyTemplate');

        $pu_questions_query = " SELECT QT.ID FROM SurveyQuestionTemplate QT
                                LEFT JOIN SurveyStepTemplate ST ON ST.ID = QT.StepID
                                LEFT JOIN SurveyTemplate S ON S.ID = ST.SurveyTemplateID
                                LEFT JOIN EntitySurveyTemplate ES ON ES.ID = S.ID
                                WHERE ES.ParentID = {$template_id} 
                                AND (QT.Name = 'ProjectsUsed' OR QT.Name = 'ProjectsUsedPoC')";

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
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();

        if (is_null($template)) return 0;

        if ($class_name == 'EntitySurvey') {
            $template_id = $template->ParentID;
        } else {
            $template_id = $template->ID;
        }

        $pu_questions_query = " SELECT QT.ID FROM SurveyQuestionTemplate QT
                                LEFT JOIN SurveyStepTemplate ST ON ST.ID = QT.StepID
                                LEFT JOIN SurveyTemplate S ON S.ID = ST.SurveyTemplateID
                                LEFT JOIN EntitySurveyTemplate ES ON ES.ID = S.ID
                                WHERE ES.ParentID = {$template_id} 
                                AND (QT.Name = 'ProjectsUsed' OR QT.Name = 'ProjectsUsedPoC')";

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
        $mandatory_filter = $this->generateMandatoryAnswersFilter('SurveyTemplate');

        // if the question is from survey and the template is entity we need to match ids correctly
        if ($class_name == 'EntitySurvey') {
            $template_id = $template->Parent()->ID;
        }

        $lang_filter = ($lang) ? "AND I.Lang = '{$lang}'" : "AND I.Lang IS NOT NULL";

        $query = <<<SQL
        SELECT COUNT(DISTINCT I.ID) FROM SurveyAnswer A
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
        INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        WHERE I.IsTest = 0 AND SSTPL.ID = $template_id
        {$lang_filter} 
        {$filters_where}
        {$mandatory_filter};
SQL;

        return DB::query($query)->value();
    }

    public function SurveyBuilderCountContinent($continent)
    {
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (is_null($template)) return;

        $question = $template->getAllQuestions()->filter('Name', ['PrimaryCountry','PrimaryCountrySydney'])->First();

        $filters_where = $this->generateFilters('SurveyTemplate');
        $mandatory_filter = $this->generateMandatoryAnswersFilter('SurveyTemplate');

        $continent_filter = ($continent) ? "AND C.Name = '{$continent}'" : "AND A.Value IS NOT NULL";

        $query = <<<SQL
        SELECT COUNT(DISTINCT I.ID) FROM SurveyAnswer A
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        INNER JOIN SurveyStepTemplate STPL ON STPL.ID = Q.StepID
        INNER JOIN SurveyTemplate SSTPL ON SSTPL.ID = STPL.SurveyTemplateID
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        INNER JOIN Continent_Countries CC ON CC.CountryCode = A.Value
        INNER JOIN Continent C ON C.ID = CC.ContinentID
        WHERE I.IsTest = 0 Q.ID = {$question->ID}
        {$continent_filter} 
        {$filters_where}
        {$mandatory_filter};
SQL;

        return DB::query($query)->value();
    }

    public function getContinentValues()
    {
        return Continent::get();
    }

    public function SurveyBuilderSurveyNPS($question_id)
    {
        $class_name = $this->getCurrentSelectedSurveyClassName();
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (is_null($template)) return 0;

        $question = SurveyQuestionTemplate::get()->byID($question_id);
        if (is_null($question)) return 0;
        $question_class = $question->Step()->SurveyTemplate()->ClassName;

        $filters_where = $this->generateFilters('SurveyTemplate');
        $mandatory_filter = $this->generateMandatoryAnswersFilter($question_class);

        $query = <<<SQL
    SELECT SUM(IF(V.`Value` < 7, 1, 0)) AS D, SUM(IF(V.`Value` > 6 AND V.`Value` < 9, 1, 0)) AS N, SUM(IF(V.`Value` > 8, 1, 0)) AS P
    FROM SurveyAnswer A
    INNER JOIN SurveyQuestionValueTemplate V ON V.ID = A.`Value`
    LEFT JOIN SurveyStep S ON S.ID = A.StepID
    LEFT JOIN Survey I ON I.ID = S.SurveyID
    WHERE A.QuestionID = $question_id AND A.`Value` IS NOT NULL AND I.IsTest = 0
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

        $question = SurveyQuestionTemplate::get()->byID($question_id);
        if (is_null($question)) return 0;
        $question_class = $question->Step()->SurveyTemplate()->ClassName;

        //$dependencies = $question->getDependsOn();
        //$dependencies_sql = $this->generateDependenciesFilter($dependencies);

        $filters_where = $this->generateFilters('SurveyTemplate');
        $mandatory_filter = $this->generateMandatoryAnswersFilter($question_class);

        $query = <<<SQL
        SELECT COUNT(DISTINCT I.ID) FROM SurveyAnswer A
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        WHERE I.IsTest = 0 AND Q.ID = $question_id AND A.`Value` IS NOT NULL
        {$filters_where}
        {$mandatory_filter};
SQL;

        return intval(DB::query($query)->value());
    }

    public function SurveyBuilderCountNPSAnswers($question_id, $value_id)
    {
        $question_id = intval($question_id);
        $value_id = intval($value_id) > 0 ? intval($value_id) : $value_id;
        $template = $this->getCurrentSelectedSurveyTemplate();
        $class_name = $this->getCurrentSelectedSurveyClassName();
        if (is_null($template)) return 0;

        $question = SurveyQuestionTemplate::get()->byID($question_id);
        if (is_null($question)) return 0;
        $question_class = $question->Step()->SurveyTemplate()->ClassName;

        $filters_where = $this->generateFilters('SurveyTemplate');
        $mandatory_filter = $this->generateMandatoryAnswersFilter($question_class);

        $query = <<<SQL
        SELECT COUNT(DISTINCT I.ID) FROM SurveyAnswer A
        INNER JOIN SurveyStep S ON S.ID = A.StepID
        INNER JOIN Survey I ON I.ID = S.SurveyID
        INNER JOIN SurveyQuestionTemplate Q ON Q.ID = A.QuestionID
        WHERE I.IsTest = 0 AND Q.ID = $question_id AND A.`Value` = {$value_id}
        {$filters_where}
        {$mandatory_filter};
SQL;

        return DB::query($query)->value();
    }

    public function ExportStatisticsSurveyBuilder(SS_HTTPRequest $request) {
        $fileDate = date('Ymdhis');
        $template = $this->getCurrentSelectedSurveyTemplate();
        if (!$template) die('Please select a template');

        $this->statModel = new DeploymentStatModel();

        $deployment_filter = $this->generateFilters('EntitySurveyTemplate', 'S');
        $survey_filter = $this->generateFilters('SurveyTemplate', 'REPORT', 'SurveyID');

        $data = $this->owner->getSurveyBuilderExportData($template->ParentID, $survey_filter, $deployment_filter);
        $filename = "Survey_" . $fileDate . ".csv";
        return CSVExporter::getInstance()->export($filename, $data, ',');
    }

    public function LinkToExport() {
        $req = Controller::curr()->getRequest(); // get the current http request object
        $req->setURL(Controller::curr()->Link('ExportStatisticsSurveyBuilder')); // set the url of it to our new Link (while ignoring query params)
        $url = $req->getURL(TRUE); // get the url back but with querystr intact.

        return $url ;
    }

}