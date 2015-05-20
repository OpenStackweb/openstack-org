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

class Survey_Controller extends Page_Controller {

    static $allowed_actions = array(
        'landing',
        'renderSurvey',
        'nextStep',
    );

    static $url_handlers = array(
        'landing' => 'landing',
        '$SURVEY_TEMPLATE_ID//$STEP/$SUB_STEP' => 'renderSurvey'
    );

    /**
     * @var ISurveyManager
     */
    private $survey_manager;

    /**
     * @var ISurvey
     */
    private $current_survey;

    /**
     * @return ISurveyManager
     */
    public function getSurveyManager(){
        return $this->survey_manager;
    }

    public function setSurveyManager(ISurveyManager $survey_manager){
        $this->survey_manager = $survey_manager;
    }

    function init() {
        parent::init();
        Requirements::css(Director::protocol().'://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css');
        Requirements::css('survey_builder/css/user-survey.css');
    }

    public function BootstrapConverted(){
        return true;
    }

    public function renderSurvey($request){

        $survey_id  = $request->param('SURVEY_TEMPLATE_ID');
        $step       = $request->param('STEP');
        $sub_step   = $request->param('SUB_STEP');

        if(!Member::currentUser()){
            $this->redirect("surveys/landing?BackURL=" . urlencode('/surveys/'.$survey_id));
            return;
        }

        if(strtolower($survey_id) === 'current'){
            //todo: get current survey
            $survey_id = 1;
        }

        $this->current_survey = $this->survey_manager->getSurveyByTemplateAndCreator($survey_id, Member::currentUserID());

        return $this->customise(array(
            'Survey' =>  $this->current_survey,
        ))->renderWith( array('Surveys_CurrentSurveyContainer','Page'));
    }

    public function landing(){
        return $this->customise(array('BackURL' => $this->request->requestVar('BackURL')))->renderWith( array('Surveys_LandingPage','Page'));
    }

    public function RenderStep(){

        $current_step = $this->current_survey->currentStep();
        $fields       = new FieldList();
        $validator    = null;
        //$current_step->template()
        $actions = new FieldList(
            new FormAction('nextStep', 'Next')
        );
        return new HoneyPotForm($this, $current_step->template()->title().'Form', $fields, $actions, $validator);
    }

    public function nextStep($data, $form)
    {

    }
}