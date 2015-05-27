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
class Survey_Controller extends Page_Controller
{

    static $allowed_actions = array(
        'landing',
        'renderSurvey',
        'SurveyStepForm',
    );

    static $url_handlers = array(
        'landing' => 'landing',
        'current//$STEP/$SUB_STEP' => 'renderSurvey'
    );

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

    function init()
    {
        parent::init();
        Requirements::css(Director::protocol() . '://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css');
        Requirements::css('survey_builder/css/user-survey.css');
        Requirements::css("themes/openstack/css/chosen.css", "screen,projection");
        Requirements::css("themes/openstack/css/jquery.autocomplete.css");

        Requirements::block(SAPPHIRE_DIR . '/thirdparty/behaviour/behaviour.js');
        Requirements::block(SAPPHIRE_DIR . '/thirdparty/prototype/prototype.js');
        Requirements::block(SAPPHIRE_DIR . '/javascript/prototype_improvements.js');

        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/additional-methods.js");
        Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.autocomplete.min.js");
    }

    public function BootstrapConverted()
    {
        return true;
    }

    public function renderSurvey($request)
    {
        $step     = $request->param('STEP');
        $sub_step = $request->param('SUB_STEP');

        //check if user is logged in
        if (!Member::currentUser()) {
            return $this->redirect("surveys/landing?BackURL=" . urlencode('/surveys/current'));
        }

        $current_template = $this->survey_manager->getCurrentSurveyTemplate();

        if (is_null($current_template))
            throw new NotFoundEntityException('SurveyTemplate', 'current not set');

        $survey_id      = $current_template->getIdentifier();
        $current_survey = $this->getCurrentSurveyInstance();

        if (is_null($current_survey)) {
            $current_survey = $this->survey_manager->getSurveyByTemplateAndCreator($survey_id, Member::currentUserID());
            Session::set('CURRENT_SURVEY_ID', $current_survey->getIdentifier());
        }

        if(!empty($step) && !$current_survey->isAllowedStep($step)){
            // redirect
            return $this->redirect('/surveys/current/'.$current_survey->currentStep()->template()->title());
        }

        return $this->customise(array(
            'Survey' => $current_survey,
        ))->renderWith(array('Surveys_CurrentSurveyContainer', 'Page'));
    }

    public function landing()
    {
        return $this->customise(array('BackURL' => $this->request->requestVar('BackURL')))->renderWith(array('Surveys_LandingPage', 'Page'));
    }


    /**
     * @return ISurvey|null
     */
    private function getCurrentSurveyInstance()
    {
        $current_survey_id = Session::get('CURRENT_SURVEY_ID');
        if (empty($current_survey_id)) return null;
        return $this->survey_repository->getById($current_survey_id);
    }

    public function SurveyStepForm()
    {
        $current_survey = $this->getCurrentSurveyInstance();
        $current_step   = $current_survey->currentStep();
        $builder        = SurveyStepUIBuilderFactory::getInstance()->build($current_step);
        $form           = $builder->build($current_step, 'NextStep');

        $form->setAttribute('data-survey-id', $current_survey->getIdentifier());
        $form->setAttribute('data-step-template-id', $current_step->getIdentifier());
        return $form;
    }

    public function NextStep($data, $form)
    {
        $current_survey = $this->getCurrentSurveyInstance();
        $current_step   = $current_survey->currentStep();

        if($current_step instanceof ISurveyRegularStep){
            $next_step = $this->survey_manager->saveCurrentStep($current_step, $data);
            return $this->redirect('/surveys/current/'.$next_step->template()->title());
        }
        else if($current_step instanceof ISurveyRegularStep){

        }
    }
}