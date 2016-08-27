<?php
/**
 * Copyright 2016 OpenStack Foundation
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
 * Class SangriaSurveyBuilderExtension
 */
final class SangriaSurveyBuilderExtension extends Extension
{
    const SurveysPageSize = 15;

    /**
     * @var SapphireSurveyRepository
     */
    private $survey_repository;
    /**
     * @var SapphireSurveyTemplateRepository
     */
    private $survey_template_repository;

    public function __construct()
    {
        $this->survey_repository = new SapphireSurveyRepository();
        $this->survey_template_repository = new SapphireSurveyTemplateRepository();
    }

    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', [
            'SurveyBuilderListSurveys',
            'SurveyBuilderListDeployments',
            'SurveyDetails',
            'DeploymentDetails',
        ]);
        Config::inst()->update(get_class($this->owner), 'allowed_actions', [
            'SurveyBuilderListSurveys',
            'SurveyBuilderListDeployments',
            'SurveyDetails',
            'DeploymentDetails',
        ]);
    }

    public function onAfterInit()
    {

    }

    public function getRequestVar($var)
    {
        $request = Controller::curr()->getRequest();
        return $request->getVar($var);
    }

    public function hasRequestVar($var)
    {
        $request = Controller::curr()->getRequest();
        return !empty($request->getVar($var));
    }

    /**
     * @return string
     */
    public function getSurveyListSortDir()
    {
        $request = Controller::curr()->getRequest();
        $dir = $request->getVar('dir');
        if (empty($dir)) return 'ASC';
        return $dir === 'ASC' ? 'DESC' : 'ASC';
    }

    public function getPagerLink($page_nbr)
    {
        $request = Controller::curr()->getRequest();
        $vars = $request->getVars();
        if (isset($vars['url'])) unset($vars['url']);
        $vars['page'] = $page_nbr;
        return http_build_query($vars);
    }

    public function getOrderLink($field)
    {
        $request = Controller::curr()->getRequest();
        $vars = $request->getVars();
        if (isset($vars['url'])) unset($vars['url']);
        $vars['order'] = $field;
        $vars['dir'] = $this->getSurveyListSortDir();
        return http_build_query($vars);
    }

    /**
     * @param SS_HTTPRequest $request
     * @param string $template_class
     * @param string $ss_tpl_name
     * @return array
     */
    private function buildList
    (
        SS_HTTPRequest $request,
        $template_class = 'SurveyTemplate',
        $ss_tpl_name = 'SurveyBuilderListSurveys'
    )
    {
        Requirements::javascript('themes/openstack/javascript/querystring.jquery.js');
        Requirements::javascript('survey_builder/js/survey.sangria.surveys.list.filter.js');

        $query_templates = new QueryObject(new SurveyTemplate);
        $query_templates->addAndCondition(QueryCriteria::equal('ClassName', $template_class));

        list($templates, $count) = $this->survey_template_repository->getAll($query_templates, 0, PHP_INT_MAX);

        $page = intval($request->getVar('page'));
        $survey_template_id = intval($request->getVar('survey_template_id'));
        $question_id = intval($request->getVar('question_id'));
        $question_value = Convert::raw2sql($request->getVar('question_value'));
        $question_value2 = Convert::raw2sql($request->getVar('question_value2'));
        $question_value = !empty($question_value) ? $question_value : $question_value2;

        $order = Convert::raw2sql($request->getVar('order'));
        $order_dir = Convert::raw2sql($request->getVar('dir'));

        if ($page === 0) $page = 1;
        $offset = ($page - 1) * self::SurveysPageSize;

        $sort_fields =
            [
                'id' => 'ID',
                'created' => 'Created'
            ];

        $query_surveys = new QueryObject(new Survey);

        $selected_template = ($survey_template_id > 0) ? $this->survey_template_repository->getById($survey_template_id) : $templates[0];

        if ($survey_template_id === 0) {
            Controller::curr()->redirect($request->getURL(true) . '?survey_template_id=' . $selected_template->ID);
        }

        $query_surveys
            ->addAndCondition
            (
                QueryCriteria::id('Survey.TemplateID', $selected_template->getIdentifier())
            )
            ->addAndCondition
            (
                QueryCriteria::id('Survey.IsTest', 0)
            );


        if ($question_id > 0 && !empty($question_value)) {
            // filter by question ...
            $query_surveys->addAlias
            (
                QueryAlias::create('Steps')
                    ->addAlias
                    (
                        QueryAlias::create('Answers')
                            ->addAlias
                            (
                                QueryAlias::create('Question')
                            )
                    )
            );

            $query_surveys->addAndCondition(
                QueryCompoundCriteria::compoundAnd([
                    QueryCriteria::id('SurveyQuestionTemplate.ID', $question_id),
                    QueryCriteria::like('SurveyAnswer.Value', $question_value)
                ])
            );
        }

        if (empty($order)) {
            $query_surveys->addOrder(QueryOrder::asc('ID'));
        } else {
            if ($order_dir === 'ASC')
                $query_surveys->addOrder(QueryOrder::asc($sort_fields[$order]));
            else
                $query_surveys->addOrder(QueryOrder::desc($sort_fields[$order]));
        }


        list($surveys, $count_surveys) = $this->survey_repository->getAll($query_surveys, $offset, self::SurveysPageSize);
        // build pager
        $pages = '';
        $max_page = intval(ceil($count_surveys / self::SurveysPageSize));

        for ($i = 1; $i < $max_page; $i++) {
            $pages .= sprintf
            (
                "<li %s ><a href=\"%s?%s\">%s</a></li>",
                ($page === $i) ? "class=\"active\"" : "",
                $this->owner->Link($ss_tpl_name),
                $this->getPagerLink($i),
                $i
            );
        }
        $pager = <<<HTML
<nav>
    <ul class="pagination pagination-sm">
        $pages
    </ul>
</nav>
HTML;

        $result = [
            'Templates' => new ArrayList($templates),
            'Surveys' => new ArrayList($surveys),
            'Questions' => new ArrayList($selected_template->getAllFilterableQuestions()),
            'Pager' => $pager
        ];

        return $result;
    }

    public function SurveyBuilderListSurveys(SS_HTTPRequest $request)
    {
        $result = $this->buildList($request);
        return $this->owner->getViewer('SurveyBuilderListSurveys')->process($this->owner->customise($result));
    }

    public function SurveyBuilderListDeployments(SS_HTTPRequest $request)
    {
        $result = $this->buildList($request, 'EntitySurveyTemplate', 'SurveyBuilderListDeployments');
        return $this->owner->getViewer('SurveyBuilderListDeployments')->process($this->owner->customise($result));
    }

    public function SurveyDetails(SS_HTTPRequest $request)
    {
        $params = $request->allParams();
        $survey_id = intval(Convert::raw2sql($params["ID"]));;
        $survey = Survey::get()->byID($survey_id);
        if ($survey->ClassName === 'EntitySurvey') {
            $survey = EntitySurvey::get()->byID($survey_id);
        }

        if (!$survey) {
            return $this->owner->httpError(404, 'Sorry that Survey could not be found!.');
        }

        $back_url = $request->getVar('BackUrl');
        $data = [

            "Name" => 'Survey',
            "Survey" => $survey,
            "BackUrl" => $back_url
        ];

        return $this->owner->Customise
        (
            $data
        )->renderWith(array('SangriaPage_SurveyBuilderSurveyDetails', 'SangriaPage', 'SangriaPage'));

    }

    public function DeploymentDetails(SS_HTTPRequest $request)
    {
        $params = $request->allParams();
        $deployment_id = intval(Convert::raw2sql($params["ID"]));
        //get survey version
        $deployment = Survey::get()->byID($deployment_id);
        if ($deployment->ClassName === 'EntitySurvey') {
            $deployment = EntitySurvey::get()->byID($deployment_id);
        }

        if (!$deployment) {
            return $this->owner->httpError(404, 'Sorry that Deployment could not be found!.');
        }

        $back_url = $request->getVar('BackUrl');
        if (empty($back_url)) {
            $back_url = $this->owner->Link("ViewDeploymentDetails");
        }

        $data = [
            "Name" => 'Deployment',
            "Survey" => $deployment,
            "BackUrl" => $back_url
        ];

        return $this->owner->Customise
        (
            $data
        )->renderWith(array('SangriaPage_SurveyBuilderSurveyDetails', 'SangriaPage', 'SangriaPage'));

    }

}