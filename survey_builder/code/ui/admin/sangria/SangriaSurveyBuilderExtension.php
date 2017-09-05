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

    static $allowed_actions =  [
        'SurveyBuilderListSurveys',
        'SurveyBuilderListSurveysExport',
        'SurveyBuilderListDeployments',
        'SurveyBuilderListDeploymentsExport',
        'SurveyDetails',
        'DeploymentDetails',
        'ViewSurveyFreeAnswersList',
        'ViewSurveyFreeAnswersStats',
    ];

    /**
     * @var ISurveyRepository
     */
    private $survey_repository;
    /**
     * @var ISurveyTemplateRepository
     */
    private $survey_template_repository;
    /**
     * @var ISurveyAnswerRepository
     */
    private $survey_answer_repository;


    public function __construct(ISurveyRepository $survey_repository,
                                ISurveyTemplateRepository $survey_template_repository,
                                ISurveyAnswerRepository $survey_answer_repository)
    {
        parent::__construct();
        $this->survey_repository =  $survey_repository;
        $this->survey_template_repository = $survey_template_repository;
        $this->survey_answer_repository = $survey_answer_repository;
    }

    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', self::$allowed_actions);
        Config::inst()->update(get_class($this->owner), 'allowed_actions', self::$allowed_actions);
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
        $order  = $request->getVar('order');
        if (empty($order)) return '+';
        return substr($order,0,1) === '+' ? '-' : '+';
    }

    public function getPagerLink($page_nbr)
    {
        $request = Controller::curr()->getRequest();
        $vars = $request->getVars();
        if (isset($vars['url'])) unset($vars['url']);
        $vars['page'] = $page_nbr;
        $query = http_build_query($vars, null, '&');
        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '%5B%5D=', $query); //foo=x&foo=y
    }

    public function getOrderLink($field)
    {
        $request = Controller::curr()->getRequest();
        $vars    = $request->getVars();
        if (isset($vars['url'])) unset($vars['url']);
        $vars['order'] = sprintf("%s%s", $this->getSurveyListSortDir(), $field);
        $query = http_build_query($vars, null, '&');
        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '%5B%5D=', $query); //foo=x&foo=y
    }

    public function getQueryString(){
        $request = Controller::curr()->getRequest();
        $vars    = $request->getVars();
        if (isset($vars['url'])) unset($vars['url']);
        $query = http_build_query($vars, null, '&');
        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '%5B%5D=', $query); //foo=x&foo=y
    }

    /**
     * @param string $action
     * @return string
     */
    public function getExportLink($action){
        return Controller::curr()->Link($action).'?'.$this->getQueryString();
    }

    public function getPageSize(){
        $request = Controller::curr()->getRequest();
        $vars    = $request->getVars();
        return isset($vars["page_size"]) ? $vars["page_size"] : 25;
    }

    /**
     * @param $survey_template
     * @param $question_id
     * @param array $question_values
     * @param $order
     * @param $page
     * @param $page_size
     * @param $survey_lang
     * @return array
     */
    private function getSurveyData
    (
        $survey_template,
        $question_id,
        array $question_values,
        $order,
        $page,
        $page_size,
        $survey_lang
    )
    {

        $order_param = null;
        if (!empty($order))
        {
            $order_param = OrderParser::parse($order, ['id', 'created', 'updated']);
        }

        return $this->survey_repository->getByTemplateAndAnswerValue
        (
            $survey_template->ID,
            $question_id,
            $question_values,
            new PagingInfo($page,  $page_size),
            $order_param,
            $survey_lang
        );
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
        $ss_tpl_name    = 'SurveyBuilderListSurveys'
    )
    {
        Requirements::javascript('themes/openstack/javascript/querystring.jquery.js');
        Requirements::javascript('survey_builder/js/survey.sangria.surveys.list.filter.js');

        $query_templates = new QueryObject(new SurveyTemplate);
        $query_templates->addAndCondition(QueryCriteria::equal('ClassName', $template_class));
        $query_templates->addOrder(QueryOrder::desc('ID'));

        list($templates, $count) = $this->survey_template_repository->getAll($query_templates, 0, PHP_INT_MAX);

        $page = intval($request->getVar('page'));
        if($page == 0 ) $page = 1;

        $survey_template_id     = intval($request->getVar('survey_template_id'));
        $question_id            = intval($request->getVar('question_id'));
        $question_text_value    = Convert::raw2sql($request->getVar('question_text_value'));
        $question_select_values = Convert::raw2sql($request->getVar('question_select_values'));
        $question_value         = !empty($question_text_value) ? $question_text_value: $question_select_values;
        $order                  = Convert::raw2sql($request->getVar('order'));
        $page_size              = Convert::raw2sql($request->getVar('page_size'));
        $survey_lang            = ($request->getVar('survey_lang')) ? Convert::raw2sql($request->getVar('survey_lang')) : 'ALL';

        if(empty($page_size)) $page_size = 25;
        if($page_size == "ALL") $page_size = PHP_INT_MAX;
        $page_size = intval($page_size);

        $selected_template = ($survey_template_id > 0) ? $this->survey_template_repository->getById($survey_template_id) : $templates[0];
        if ($survey_template_id === 0) {
            Controller::curr()->redirect($request->getURL(true) . '?survey_template_id=' . $selected_template->ID);
        }

        if(!is_array($question_value)) $question_value = [$question_value];

        list($surveys, $count, $count_completed, $count_deployments) = $this->getSurveyData
        (
            $selected_template,
            $question_id,
            $question_value,
            $order,
            $page,
            $page_size,
            $survey_lang
        );

        // build pager
        $pages    = '';
        $max_page = intval(ceil($count / $page_size));

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
           <select id="ddl_page_size" name="ddl_page_size" title="select page size">
            <option value="10">10</option>
            <option selected value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="ALL">ALL</option>
        </select>
<nav>
    <ul class="pagination pagination-sm">
        $pages
    </ul>
</nav>
HTML;
        if(empty($pages)) $pager = '';

        $result = [
            'Templates'        => new ArrayList($templates),
            'Surveys'          => $surveys,
            'Questions'        => $selected_template->getAllFilterableQuestions(),
            'Pager'            => $pager,
            'Total'            => $count,
            'Completed'        => $count_completed,
            'Deployments'      => $count_deployments
        ];

        return $result;
    }

    public function SurveyBuilderListSurveys(SS_HTTPRequest $request)
    {
        Requirements::css("sangria/ui/source/css/sangria.css");
        $result = $this->buildList($request);
        return $this->owner->getViewer('SurveyBuilderListSurveys')->process($this->owner->customise($result));
    }

    public function SurveyBuilderListSurveysExport(SS_HTTPRequest $request){
        $survey_template_id     = intval($request->getVar('survey_template_id'));
        $question_id            = intval($request->getVar('question_id'));
        $question_text_value    = Convert::raw2sql($request->getVar('question_text_value'));
        $question_select_values = Convert::raw2sql($request->getVar('question_select_values'));
        $question_value         = !empty($question_text_value) ? $question_text_value: $question_select_values;
        $order                  = Convert::raw2sql($request->getVar('order'));
        $survey_lang            = Convert::raw2sql($request->getVar('survey_lang'));
        $selected_template      = $this->survey_template_repository->getById($survey_template_id);

        if(!is_array($question_value)) $question_value = [$question_value];

        list($surveys, $count, $count_completed, $count_deployments) = $this->getSurveyData
        (
            $selected_template,
            $question_id,
            $question_value,
            $order,
            1,
            PHP_INT_MAX,
            $survey_lang
        );

        $result = [];
        foreach($surveys as $survey){
            $result[] = [
                'ID'                => $survey->ID,
                'LastEdited'        => $survey->LastEdited,
                'Created By'        => $survey->CreatedBy()->Email,
                'Organization'      => $survey->getAnswerFor("Organization"),
                'Current Step'      => $survey->CurrentStep()->Template()->Name,
                'Is Completed ?'    => $survey->isComplete(),
                'Has Deployments ?' => $survey->EntitySurveysCount(),
                'Lang'              => $survey->Lang,
            ];
        }

        return CSVExporter::getInstance()->export("surveys.csv", $result);
    }

    public function SurveyBuilderListDeployments(SS_HTTPRequest $request)
    {
        Requirements::css("sangria/ui/source/css/sangria.css");
        $result = $this->buildList($request, 'EntitySurveyTemplate', 'SurveyBuilderListDeployments');

        return $this->owner->getViewer('SurveyBuilderListDeployments')->process($this->owner->customise($result));
    }

    public function SurveyBuilderListDeploymentsExport(SS_HTTPRequest $request){
        $survey_template_id     = intval($request->getVar('survey_template_id'));
        $question_id            = intval($request->getVar('question_id'));
        $question_text_value    = Convert::raw2sql($request->getVar('question_text_value'));
        $question_select_values = Convert::raw2sql($request->getVar('question_select_values'));
        $question_value         = !empty($question_text_value) ? $question_text_value: $question_select_values;
        $order                  = Convert::raw2sql($request->getVar('order'));
        $survey_lang            = Convert::raw2sql($request->getVar('survey_lang'));
        $selected_template      = $this->survey_template_repository->getById($survey_template_id);

        if(!is_array($question_value)) $question_value = [$question_value];

        list($surveys, $count, $count_completed, $count_deployments) = $this->getSurveyData
        (
            $selected_template,
            $question_id,
            $question_value,
            $order,
            1,
            PHP_INT_MAX,
            $survey_lang
        );

        $result = [];
        foreach($surveys as $survey){
            $result[] = [
                'ID'                => $survey->ID,
                'LastEdited'        => $survey->LastEdited,
                'Created By'        => $survey->CreatedBy()->Email,
                'Label'             => $survey->getAnswerFor("Label"),
                'Current Step'      => $survey->CurrentStep()->Template()->Name,
                'Is Completed ?'    => $survey->isComplete(),
                'Lang'              => $survey->Lang,
            ];
        }

        return CSVExporter::getInstance()->export("deployments.csv", $result);
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

    /**
     * @param SS_HTTPRequest $request
     * @return mixed
     */
    public function ViewSurveyFreeAnswersList(SS_HTTPRequest $request){

        Requirements::clear();
        Requirements::css('sangria/ui/source/css/sangria.css');

        // js
        Requirements::javascript("themes/openstack/bower_assets/jquery/dist/jquery.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-migrate/jquery-migrate.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-cookie/jquery.cookie.js");
        //tags inputs
        // defined here bc amd/requirejs module definition is broken
        Requirements::javascript('node_modules/bootstrap-3-typeahead/bootstrap3-typeahead.min.js');

        $now  = MySQLDatabase56::nowRfc2822();

        return $this->owner->getViewer('ViewSurveyFreeAnswersList')->process
        (
            $this->owner->Customise([
                'SurveyTemplates' => SurveyTemplate::get()->filter(
                    [
                        'ClassName'        => 'SurveyTemplate'
                    ]
                )->sort('StartDate', 'ASC')
            ])
        );
    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function ViewSurveyFreeAnswersStats(SS_HTTPRequest $request){

        Requirements::clear();
        Requirements::set_force_js_to_bottom(true);
        Requirements::css('sangria/ui/source/css/sangria.css');
        // js
        Requirements::javascript("themes/openstack/bower_assets/jquery/dist/jquery.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-migrate/jquery-migrate.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-cookie/jquery.cookie.js");

        $query_string = $request->getVars();
        $template_id  = intval($query_string['template_id']);
        $question_id  = intval($query_string['question_id']);
        $question     = SurveyQuestionTemplate::get()->byID($question_id);

        if(is_null($question)) return new SS_HTTPResponse("Question not found", 404);

        $tag_count_results = $this->survey_answer_repository->getCountForTags($question_id);
        $answer_count = $question->Answers()->Count();

        $results = [];
        $total_tag_count = 0;
        foreach ($tag_count_results as $row){
            $total_tag_count += intval( $row['Qty']);
            $results[] = new ArrayData([
                'Count'     => $row['Qty'],
                'Tag'       => $row['Tag'],
                'ID'        => $row['ID'],
                'AnswerIDs' => $row['AnswerIDs']
            ]);
        }

        return $this->owner->getViewer('ViewSurveyFreeAnswersStats')->process
        (
            $this->owner->Customise([
                'Data'          => new ArrayList($results),
                'QuestionTitle' => $question->Label,
                'AnswerCount'   => $answer_count,
                'QuestionID'    => $question_id,
                'TemplateID'    => $template_id
            ])
        );
    }

}