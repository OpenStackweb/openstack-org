<?php
/**
 * Copyright 2017 OpenStack Foundation
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
class SurveyPage extends Page
{
    static $db = array
    (
        'ThankYouText' => 'HTMLText'
    );

    private static $has_one = array
    (
        'SurveyTemplate' => 'SurveyTemplate',
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');
        $fields->addFieldsToTab('Root.Main', $ddl = new DropdownField('SurveyTemplateID', 'Survey Template', SurveyTemplate::get()->filter(
            array
            (
                'ClassName' => 'SurveyTemplate',
                'Enabled'   => true
            )
        )->map('ID','Title')));
        $ddl->setEmptyString('-- Select a Survey Template');

        $fields->addFieldsToTab('Root.Main', $thankyou = new HtmlEditorField('ThankYouText', 'Thank You Text'));

        return $fields;
    }
}

class SurveyPage_Controller extends Page_Controller
{
    static $allowed_actions_without_auth = [
        'LandingPage',
        'RegisterForm',
        'MemberStart',
        'StartSurvey'
    ];

    /**
     * @var IMemberManager
     */
    private $member_manager;

    /**
     * @return IMemberManager
     */
    public function getMemberManager()
    {
        return $this->member_manager;
    }

    /**
     * @param IMemberManager $manager
     */
    public function setMemberManager(IMemberManager $manager)
    {
        $this->member_manager = $manager;
    }

    static $allowed_actions = [
        'LandingPage',
        'RenderSurvey',
        'SurveyStepForm',
        'SkipStep',
        'RegisterForm',
        'StartSurvey',
        'NextStep',
        'SurveyDynamicEntityStepForm',
        'NextDynamicEntityStep',
        'AddEntity',
        'EditEntity',
        'DeleteEntity',
        'RenderSurveyEnd',
    ];

    static $url_handlers = [
        'landing' => 'LandingPage',
        '$STEP_SLUG/add-entity' => 'AddEntity',
        '$STEP_SLUG/skip-step' => 'SkipStep',
        '$STEP_SLUG/edit/$ENTITY_SURVEY_ID//$SUB_STEP_SLUG' => 'EditEntity',
        '$STEP_SLUG/delete/$ENTITY_SURVEY_ID' => 'DeleteEntity',
        'POST SurveyStepForm' => 'SurveyStepForm',
        'GET  SurveyStepForm' => 'SurveyStepForm',
        'POST SurveyDynamicEntityStepForm' => 'SurveyDynamicEntityStepForm',
        'GET  SurveyDynamicEntityStepForm' => 'SurveyDynamicEntityStepForm',
        'GET thank-you-end' => 'RenderSurveyEnd',
        'GET  $STEP_SLUG' => 'RenderSurvey',
        'POST $Action//$ID/$OtherID' => 'handleAction',
    ];

    /**
     * @var ISurvey
     */
    private $current_survey;

    /**
     * @var IEntitySurvey
     */
    private $current_entity_survey;

    /**
     * @var ISurveyManager
     */
    private $survey_manager;

    /**
     * @var ISurveyRepository
     */
    private $survey_repository;

    /**
     * @return ISurveyManager
     */
    public function getSurveyManager()
    {
        return $this->survey_manager;
    }

    /**
     * @param ISurveyManager $survey_manager
     */
    public function setSurveyManager(ISurveyManager $survey_manager)
    {
        $this->survey_manager = $survey_manager;
    }

    /**
     * @return ISurveyRepository
     */
    public function getSurveyRepository()
    {
        return $this->survey_repository;
    }

    /**
     * @param ISurveyRepository $survey_repository
     */
    public function setSurveyRepository(ISurveyRepository $survey_repository)
    {
        $this->survey_repository = $survey_repository;
    }

    public function httpError($code, $message = null)
    {
        $response = new SS_HTTPResponse();
        $response->setStatusCode($code);
        $response->addHeader('Content-Type', 'text/html');
        return $response;
    }

    function init()
    {
        parent::init();

        Requirements::block(SAPPHIRE_DIR . '/thirdparty/behaviour/behaviour.js');
        Requirements::block(SAPPHIRE_DIR . '/thirdparty/prototype/prototype.js');
        Requirements::block(SAPPHIRE_DIR . '/javascript/prototype_improvements.js');
        Requirements::block("themes/openstack/javascript/jquery.autocomplete.min.js");

        FontAwesomeDependencies::renderRequirements();
        JQueryValidateDependencies::renderRequirements(true, false);
        JSChosenDependencies::renderRequirements();
        JQueryUIDependencies::renderRequirements(JQueryUIDependencies::SmoothnessTheme);

        Requirements::css('survey_builder/css/user-survey.css');
        Requirements::javascript('survey_builder/js/survey.validation.rules.jquery.js');
        Requirements::javascript('node_modules/clipboard/dist/clipboard.min.js');
        Requirements::javascript('node_modules/pure/libs/pure.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript("node_modules/js-cookie/src/js.cookie.js");
        Requirements::javascript('gettext/javascript/gettext.js');
        Requirements::javascript('survey_builder/js/survey.controller.js');
        // populate the js messages

        $messages_ids = [
            "This field is required.",
            "Please fix this field.",
            "Please enter a valid email address.",
            "Please enter a valid URL.",
            "Please enter a valid date.",
            "Please enter a valid number.",
            "Please enter only digits.",
            "Please enter the same value again.",
            "Please enter no more than {0} characters.",
            "Please enter at least {0} characters.",
            "Please enter a value between {0} and {1} characters long.",
            "Please enter a value between {0} and {1}.",
            "Please enter a value less than or equal to {0}.",
            "Please enter a value greater than or equal to {0}.",
            "Are you sure?",
            "You Must Specify a New Organization Name!.",
            "You must select a valid member!",
            "Delete",
            "First Name is required.",
            "First Name is not valid.",
            "Last Name is required.",
            "Last Name is not valid.",
            "Primary Email Address is required.",
            "Primary Email Address is not valid.",
            "That address is already in use by another user.",
            "Please confirm that you are not a robot.",
        ];

        foreach ($messages_ids as $msgid)
            $messages[$msgid] = GetTextTemplateHelpers::_t("survey_ui", $msgid);

        Requirements::customScript(sprintf("GetText.addMessages(%s);", json_encode($messages)));

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    protected function handleAction($request, $action)
    {
        if (!Member::currentUser()) {
            if (!in_array($action, self::$allowed_actions_without_auth)) {
                $back_url = $request->getVar('url');
                if (empty($back_url)) $back_url = urlencode($this->Link());
                return $this->redirect($this->Link("landing") . "?BackURL=" . $back_url);
            }
        }
        return parent::handleAction($request, $action);
    }


    /**
     * @return HTMLText
     */
    public function LandingPage()
    {
        return $this->customise(['BackURL' => $this->request->requestVar('BackURL')])->renderWith(['SurveyPage_LandingPage', 'Page']);
    }

    public function HasOnGoingSurvey(){
        try {
            $current_survey = $this->getCurrentSurveyInstance();
            return !is_null($current_survey);
        }
        catch(Exception $ex){
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
            return false;
        }
    }

    public function getSurveyLink(){
        if(!$this->HasOnGoingSurvey()){
            return null;
        }
        $current_survey = $this->getCurrentSurveyInstance();
        return Controller::join_links(
            $this->Link() , $current_survey->currentStep()->template()->title()
        );
    }

    public function getHomeLink(){
        return $this->Link("landing");
    }

    /**
     * @param $step_name
     * @return string
     * @throws NotFoundEntityException
     */
    public function SurveyStepClass($step_name){
        $css_step_class = '';
        $current_survey = $this->getCurrentSurveyInstance();
        $current_step   = $current_survey->currentStep();
        if($current_step->template()->title() == $step_name)
            $css_step_class = 'current';
        else{
            //check if future or complete
            if($current_survey->isAllowedStep($step_name)){
                $css_step_class = 'completed';
            }
            else{
                $css_step_class = 'future';
            }
        }
        return $css_step_class;
    }

    /**
     * @param $request
     * @return HTMLText|SS_HTTPResponse|void
     */
    public function RenderSurvey(SS_HTTPRequest $request)
    {
        //check if user is logged in

        try {

            $step_name      = $request->param('STEP_SLUG');
            SS_Log::log(sprintf("STEP_SLUG %s", $step_name), SS_Log::DEBUG);

            $current_survey = $this->getCurrentSurveyInstance();

            if(empty($step_name))
            {
                return $this->redirect(
                    $this->Link("landing")
                );
            }

            $this->survey_manager->registerCurrentStep($this->current_survey, $step_name);
            $step = $current_survey->getStep($step_name);

            if ($current_survey->isLastStep() && $step->template()->getType() == 'SurveyThankYouStepTemplate' && !$current_survey->isEmailSent()) {
                $this->survey_manager->sendFinalStepEmail(new SurveyThankYouEmailSenderService, $current_survey);
            }

            return $this->customise([
                'Survey' => $current_survey,
            ])->renderWith(['Surveys_CurrentSurveyContainer', 'SurveyPage']);
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->LandingPage();
        }
        catch(Exception $ex){
            SS_Log::log($ex, SS_Log::ERR);
            return $this->httpError(404, "Survey not found!");
        }
    }

    public function RenderSurveyEnd(SS_HTTPRequest $request){
        $current_survey = $this->getCurrentSurveyInstance();

        return $this->customise([
            'Survey' => $current_survey,
            'SurveyReportPage' => SurveyReportPage::getLive()
        ])->renderWith(['UserSurveyPage_ThankYou', 'SurveyPage']);
    }

    /**
     * @param $step_name
     * @return string
     * @throws NotFoundEntityException
     */
    public function SurveyStepClassIcon($step_name){

        $current_survey = $this->getCurrentSurveyInstance();

        if(is_null($current_survey))
            throw new NotFoundEntityException();

        $current_step   = $current_survey->currentStep();

        if(is_null($current_step))
            throw new NotFoundEntityException();

        if($current_step->template()->title() == $step_name)
            return 'fa-pencil navigation-icon-current';

        $desired_step = $current_survey->getStep($step_name);

        if(is_null($desired_step))
            throw new NotFoundEntityException();

        if($desired_step->isComplete())
            return 'fa-check-circle navigation-icon-completed';

        return 'fa-plus-circle navigation-icon-incompleted';
    }

    /**
     * @return ISurvey|null
     * @throws NotFoundEntityException
     */
    private function getCurrentSurveyInstance()
    {

        // get current template
        $current_template = $this->SurveyTemplate();

        if (is_null($current_template))
            throw new NotFoundEntityException('SurveyTemplate', 'current template not set');

        if($current_template->isVoid())
            throw new NotFoundEntityException('SurveyTemplate', 'current template is void!');

        if(!is_null($this->current_survey)) return $this->current_survey;

        $this->current_survey = $this->survey_repository->getByTemplateAndCreator
        (
            $current_template->getIdentifier(),
            Member::currentUserID()
        );

        // if not, create the survey and do the population
        if(is_null($this->current_survey))
        {

            $this->current_survey     = $this->survey_manager->buildSurvey
            (
                $current_template->getIdentifier(),
                Member::currentUserID()
            );
            // check if we should pre populate with former data ....

            if($current_template->shouldPrepopulateWithFormerData())
            {
                $this->survey_manager->doAutopopulation
                (
                    $this->current_survey,
                    SurveyDataAutoPopulationStrategyFactory::build(SurveyDataAutoPopulationStrategyFactory::NEW_STRATEGY)
                );
            }
        }

        $this->current_survey = $this->survey_manager->updateSurveyWithTemplate($this->current_survey, $current_template);

        if(!is_null($this->current_survey)) {
            $this->current_survey->Lang = GetText::current_locale();
            $this->current_survey->write();
        }

        return $this->current_survey;
    }

    /**
     * @param int $entity_survey_id
     * @return IEntitySurvey
     * @throws NotFoundEntityException
     */
    private function getCurrentEntitySurveyInstance($entity_survey_id)
    {
        if(!is_null($this->current_entity_survey)) return $this->current_entity_survey;

        $this->current_survey        = $this->getCurrentSurveyInstance();
        $this->current_entity_survey = $this->current_survey->currentStep()->getEntitySurvey($entity_survey_id);
        if(!is_null($this->current_entity_survey)) {
            $this->current_entity_survey->Lang = GetText::current_locale();
            $this->current_entity_survey->write();
        }
        return $this->current_entity_survey;
    }

    /**
     * @return Form|string
     * @throws NotFoundEntityException
     */
    public function SurveyStepForm()
    {
        $current_survey = $this->getCurrentSurveyInstance();
        $current_step   = $current_survey->currentStep();
        $is_last_step   = ($current_step->template() instanceof ISurveyReviewStepTemplate);

        // if its last step and he/she already submitted the survey, we show the thank you message
        if ( $is_last_step && $current_survey->isComplete() && !empty($this->ThankYouText)) {
            $arrayData = new ArrayData(array(
                'Name' => $current_survey->CreatedBy()->getName(),
                'ThankYouText' => $this->ThankYouText
            ));

            return $arrayData->renderWith('SurveyPage_FinalLandingPage');
        }

        $builder = SurveyStepUIBuilderFactory::getInstance()->build($current_step);
        if(is_null($builder)) return '<p>There is no form for this step yet!</p>';
        return $builder->build($current_step, 'NextStep');
    }

    /**
     * @param $data
     * @param $form
     * @return SS_HTTPResponse
     * @throws NotFoundEntityException
     */
    public function NextStep($data, $form)
    {
        $current_survey = $this->getCurrentSurveyInstance();
        $current_step   = $current_survey->currentStep();
        SS_Log::log(sprintf("current step %s", $current_survey->currentStep()->Template()->Name), SS_Log::DEBUG);
        $form->clearMessage();
        $next_step = null;

        if($current_step instanceof ISurveyRegularStep)
            $next_step = $this->survey_manager->completeStep($current_step, $data);
        else {
            if($current_step->canSkip())
                $next_step = $current_survey->getNextStep($current_step->template()->title());
            else
                $next_step = $current_step;
        }

        $should_move_to_next_step = false;
        if(isset($data['NEXT_STEP']) &&
            $desired_step = $current_survey->getStep($data['NEXT_STEP'])) {
            if ($current_survey->canShowStep($desired_step)) {
                $next_step = $desired_step;
                $should_move_to_next_step = true;
            }
        }

        if ($current_survey->isLastStep() && $current_step->template()->getType() == 'SurveyReviewStepTemplate') {
            if(!$current_survey->isEmailSent())
                $this->survey_manager->sendFinalStepEmail(new SurveyThankYouEmailSenderService, $current_survey);

            if(!$current_survey->isComplete())
                $this->survey_manager->completeSurvey($current_step);

            if(!$should_move_to_next_step)
                return $this->redirect($this->Link().'thank-you-end');
        }
        SS_Log::log(sprintf("end current step %s", $current_survey->currentStep()->Template()->Name), SS_Log::DEBUG);
        return $this->redirect($this->Link().$next_step->template()->title());
    }

    /**
     * @param $request
     * @return SS_HTTPResponse
     * @throws NotFoundEntityException
     */
    public function SkipStep($request)
    {
        $current_survey = $this->getCurrentSurveyInstance();
        $current_step   = $current_survey->currentStep();
        $can_skip       = $current_step->canSkip();
        if($current_step instanceof ISurveyDynamicEntityStep) $can_skip = true;
        if(!$can_skip)  return $this->redirectBack();

        $next_step      = $this->survey_manager->completeStep($current_step, $data = array());
        return $this->redirect($this->Link().$next_step->template()->title());
    }

    // Dynamic Entities

    /**
     * @param $request
     * @return SS_HTTPResponse
     * @throws NotFoundEntityException
     */
    public function DeleteEntity($request)
    {
        try
        {
            if(!Member::currentUser()) return $this->httpError(403);

            $step                 = $request->param('STEP_SLUG');
            $sub_step             = $request->param('SUB_STEP_SLUG');
            $entity_survey_id     = intval($request->param('ENTITY_SURVEY_ID'));

            $this->current_survey = $this->getCurrentSurveyInstance();
            $current_step         = $this->current_survey->currentStep();

            if(!($current_step instanceof ISurveyDynamicEntityStep))
            {
                if($this->current_survey->isAllowedStep($step))
                {
                    $current_step = $this->current_survey->getStep($step);
                    $this->survey_manager->registerCurrentStep($this->current_survey, $step);
                }
                else
                    throw new LogicException(sprintf('template %s', $current_step->template()->title()));
            }

            $this->survey_manager->deleteEntitySurvey($current_step, $entity_survey_id);

            return $this->redirect($this->Link().$step);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->httpError(404);
        }
    }

    /**
     * @param $request
     * @return SS_HTTPResponse|void
     */
    public function AddEntity($request){

        try
        {
            if(!Member::currentUser()) return $this->httpError(403);

            $step                 = $request->param('STEP_SLUG');
            $sub_step             = $request->param('SUB_STEP_SLUG');

            $current_survey = $this->getCurrentSurveyInstance();
            $current_step   = $current_survey->currentStep();

            if(!($current_step instanceof ISurveyDynamicEntityStep))
            {
                if($this->current_survey->isAllowedStep($step)) {
                    $current_step = $this->current_survey->getStep($step);
                    $this->survey_manager->registerCurrentStep($this->current_survey, $step);

                }
                else
                    throw new LogicException(sprintf('template %s', $current_step->template()->title()));
            }
            //create the current survey entity
            $this->current_entity_survey = $this->survey_manager->buildEntitySurvey($current_step, Member::currentUserID());
            $url = sprintf('%s%s/edit/%s',$this->Link(), $current_step->template()->title(), $this->current_entity_survey->getIdentifier());
            $this->redirect($url);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->httpError(404);
        }
    }

    /**
     * @param $request
     * @return HTMLText|SS_HTTPResponse|void
     * @throws NotFoundEntityException
     */
    public function EditEntity($request)
    {
        try
        {
            if (!Member::currentUser()) return $this->httpError(403);

            $step             = $request->param('STEP_SLUG');
            $sub_step         = $request->param('SUB_STEP_SLUG');
            $entity_survey_id = intval($request->param('ENTITY_SURVEY_ID'));

            $this->current_survey = $this->getCurrentSurveyInstance();
            $current_step         = $this->current_survey->currentStep();

            if (!($current_step instanceof ISurveyDynamicEntityStep))
            {
                if ($this->current_survey->isAllowedStep($step)) {
                    $current_step = $this->current_survey->getStep($step);
                    $this->survey_manager->registerCurrentStep($this->current_survey, $step);
                } else {
                    throw new LogicException(sprintf('template %s', $current_step->template()->title()));
                }
            }

            $this->current_entity_survey = $current_step->getEntitySurvey($entity_survey_id);

            if (is_null($this->current_entity_survey))
            {
                //check if its a entity survey from a team that i belong
                $current_member = Member::currentUser();
                $this->current_entity_survey = $current_member
                    ->TeamEntitySurveys()
                    ->filter('EntitySurveyID', $entity_survey_id)
                    ->first();
            }

            if (is_null($this->current_entity_survey))
            {
                throw new LogicException
                (
                    sprintf
                    (
                        "member id %s is not allowed to edit entity survey id %s",
                        Member::currentUserID(),
                        $entity_survey_id
                    )
                );
            }

            $entity_step          = $this->current_entity_survey->currentStep();
            $entity_step_template = $entity_step->template();

            // check substep variable
            if (empty($sub_step))
            {
                // is not set, redirect to first step uri
                $steps = $this->current_entity_survey->getSteps();
                return $this->redirect($request->getUrl() . '/' . $steps[0]->template()->title());
            }
            else
            {
                if ($sub_step !== $entity_step_template->title())
                {
                    // if set, but differs from current step check if we are allowed to use that step
                    $desired_sub_step = $this->current_entity_survey->getStep($sub_step);
                    if (!is_null($desired_sub_step) && $this->current_entity_survey->isAllowedStep($sub_step))
                    {
                        $this->current_entity_survey->registerCurrentStep($desired_sub_step);
                    }
                    else
                    {
                        // if we are not allowed to go to desired step , redirect to current step

                        $current_url = Controller::join_links
                        (
                            Director::absoluteBaseURL(),
                            $this->Link(),
                            $step,
                            'edit',
                            $entity_survey_id,
                            $entity_step_template->title()
                        );

                        return $this->redirect($current_url);
                    }
                }
            }

            $this->current_entity_survey = $this->survey_manager->updateSurveyWithTemplate
            (
                $this->current_entity_survey,
                $this->current_entity_survey->template()
            );

            if(!is_null($this->current_entity_survey)) {
                $this->current_entity_survey->Lang = GetText::current_locale();
                $this->current_entity_survey->write();
            }

            if ($sub_step === 'skip-step' && $this->current_entity_survey->currentStep()->canSkip())
            {
                $next_step = $this->survey_manager->completeStep
                (
                    $this->current_entity_survey->currentStep(),
                    $data = array()
                );
                return $this->go2DynEntityStep($this->current_entity_survey, $next_step);
            }


            return $this->customise
            (
                array
                (
                    'Survey'       => $this->current_survey,
                    'EntitySurvey' => $this->current_entity_survey
                )
            )->renderWith(array('Surveys_CurrentSurveyDynamicEntityContainer', 'SurveyPage'));
        }
        catch(LogicException $ex1){
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->httpError(404);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->httpError(404);
        }
    }

    /**
     * @return Form|string
     * @throws NotFoundEntityException
     */
    public function SurveyDynamicEntityStepForm()
    {
        if(!Member::currentUser()) return $this->httpError(403);

        try
        {
            $request  = $this->getRequest();

            $step     = $request->param('STEP_SLUG');
            if(is_null($step))
                $step = $request->requestVar('STEP_SLUG');

            $sub_step = $request->param('SUB_STEP_SLUG');
            if(is_null($sub_step))
                $sub_step = $request->requestVar('SUB_STEP_SLUG');

            if(empty($step) || empty($sub_step))
                throw new LogicException(sprintf('step/sub_step empty (%s - %s) - member_id %s',$step, $sub_step, Member::currentUserID()));

            $this->current_survey = $this->getCurrentSurveyInstance();
            $current_step         = $this->current_survey->currentStep();

            if (!($current_step instanceof ISurveyDynamicEntityStep))
            {
                if ($this->current_survey->isAllowedStep($step))
                {
                    $current_step = $this->current_survey->getStep($step);
                    $this->survey_manager->registerCurrentStep($this->current_survey, $step);
                } else
                {
                    throw new LogicException(sprintf('template %s - member_id %s', $current_step->template()->title(), Member::currentUserID()));
                }
            }

            // check entity survey id
            $entity_survey_id = intval($request->param('ENTITY_SURVEY_ID'));
            if ($entity_survey_id === 0)
            {
                $entity_survey_id = intval($request->requestVar('ENTITY_SURVEY_ID'));
            }
            if ($entity_survey_id === 0)
            {
                throw new LogicException(sprintf('entity survey id is %s - member_id %s', $entity_survey_id, Member::currentUserID()));
            }

            $this->current_entity_survey = $current_step->getEntitySurvey($entity_survey_id);

            if (is_null($this->current_entity_survey))
            {
                //check if its a entity survey from a team that i belong
                $current_member = Member::currentUser();
                $this->current_entity_survey = $current_member->TeamEntitySurveys()->filter('EntitySurveyID',
                    $entity_survey_id)->first();
            }

            if (is_null($this->current_entity_survey))
            {
                throw new LogicException(sprintf('entity survey id is %s - member_id %s', $entity_survey_id, Member::currentUserID()));
            }

            if($this->current_entity_survey->currentStep()->template()->title() !== $sub_step)
            {

                if ($this->current_entity_survey->isAllowedStep($sub_step))
                {
                    $this->survey_manager->registerCurrentStep($this->current_entity_survey, $sub_step);
                }
                else
                {
                    throw new LogicException
                    (
                        sprintf
                        (
                            'current step %s differs from requested one %s - member_id %s',
                            $this->current_entity_survey->currentStep()->template()->title(),
                            $sub_step,
                            Member::currentUserID()
                        )
                    );
                }
            }

            $builder = SurveyStepUIBuilderFactory::getInstance()->build($this->current_entity_survey->currentStep());

            if (is_null($builder))
            {
                throw new LogicException(sprintf('There is not set any form yet! - member_id %s', Member::currentUserID()));
            }

            $form = $builder->build
            (
                $this->current_entity_survey->currentStep(),
                'NextDynamicEntityStep',
                'SurveyDynamicEntityStepForm'
            );

            $form->Fields()->add(new HiddenField('ENTITY_SURVEY_ID', 'ENTITY_SURVEY_ID', $entity_survey_id));
            $form->Fields()->add(new HiddenField('STEP_SLUG', 'STEP_SLUG', $step));
            $form->Fields()->add(new HiddenField('SUB_STEP_SLUG', 'SUB_STEP_SLUG', $sub_step));

            return $form;
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->httpError(404);
        }
    }

    /**
     * @param $data
     * @param $form
     * @return SS_HTTPResponse
     */
    public function NextDynamicEntityStep($data, $form)
    {
        try
        {

            $step     = $this->request->param('STEP_SLUG');
            if(is_null($step))
                $step = $this->request->requestVar('STEP_SLUG');

            $sub_step = $this->request->param('SUB_STEP_SLUG');
            if(is_null($sub_step))
                $sub_step = $this->request->requestVar('SUB_STEP_SLUG');

            $entity_survey = $this->getCurrentEntitySurveyInstance(intval($data['survey_id']));

            if(is_null($entity_survey))
                throw new LogicException('entity survey not found!');

            $current_step   = $entity_survey->currentStep();
            $next_step      = $this->survey_manager->completeStep($current_step, $data);
            $current_survey = $this->getCurrentSurveyInstance();

            // outside navigation ( if  user hits some tab )
            if(isset($data['NEXT_STEP']) &&
                $desired_step = $current_survey->getStep($data['NEXT_STEP'])) {
                if ($current_survey->canShowStep($desired_step)) {
                    return $this->redirect($this->Link() . $desired_step->template()->title());
                }
            }

            if($entity_survey->isLastStep())
            {

                $this->survey_manager->completeSurvey($current_step);
                return $this->redirect($this->Link().$this->current_survey->currentStep()->template()->title());
            }

            return $this->go2DynEntityStep($entity_survey, $next_step);
        }

        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->httpError(404);
        }
    }

    /**
     * @param IEntitySurvey $entity
     * @param ISurveyStep $next_step
     * @return SS_HTTPResponse
     */
    private function go2DynEntityStep(IEntitySurvey $entity,ISurveyStep $next_step){
        $dyn_step_holder_title = $this->current_survey->currentStep()->template()->title();
        $next_step_title       = $next_step->template()->title();
        return $this->redirect(sprintf('%s%s/edit/%s/%s', $this->Link(), $dyn_step_holder_title, $entity->getIdentifier(),$next_step_title));
    }

    // landing page

    public function RegisterForm()
    {
        $form =  new SurveyRegistrationForm($this, 'RegisterForm', $this->member_manager);
        $data = Session::get("FormInfo.{$form->getName()}.data");
        return $form->loadDataFrom($data ?: array ());
    }

}