<?php
/**
 * Copyright 2014 Openstack Foundation
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

class DeploymentSurveyYourThoughtsForm  extends Form {

    function __construct($controller, $name){

        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

        if(Director::isLive()) {
            Requirements::javascript("themes/openstack/javascript/jquery.ui.touch-punch.min.js");
        }
        else{
            Requirements::javascript("themes/openstack/javascript/jquery.ui.touch-punch.js");
        }

        $survey = Controller::curr()->GetCurrentSurvey();

        $nextButton = new FormAction('NextStep', '  Next Step  ');
        //BusinessDrivers

        $custom_field = array(
            new LiteralField('header','<div id="catalog">'),
            new LiteralField('custom_question', '<p>What are your top business drivers for using OpenStack?</p><p>Please rank up to 5.</p><p>1 = top business driver, 2 = next, 3 = third, and so on</p><p><strong>Select At Least One</strong></p>'),
            new LiteralField('ul_begin','<ul id="answers">'),
        );

        foreach(DeploymentSurvey::$business_drivers_options as $key => $value){
            $hidden = strpos($survey->BusinessDrivers, $key) !== false?' hidden':'';
            array_push($custom_field,new LiteralField('li_option','<li data-key="'.md5($key).'" class="answer_option'.$hidden.'" id="'.md5($key).'">'.$value.'</li>'));
        }
        $hidden = strpos($survey->BusinessDrivers,'Other') !== false?' hidden':'';

        array_push($custom_field, new LiteralField('li_option','<li data-key="6311ae17c1ee52b36e68aaf4ad066387" class="answer_option'.$hidden.'" id="6311ae17c1ee52b36e68aaf4ad066387">Something else not listed here (please specify)</li>'));
        array_push($custom_field, new LiteralField('ul_end','</ul>'));

        array_push($custom_field, new LiteralField('options','<div id="options"><div class="ui-widget-content"><ol>'));
        if(empty($survey->BusinessDrivers)) {
            array_push($custom_field, new LiteralField('placeholder', '<div class="placeholder">Drag and drop your answers here</div>'));
        }
        else{
            $drivers = explode(',',$survey->BusinessDrivers);
            foreach($drivers as $driver){
                $driver = trim($driver);
                if(!empty($driver)){
                    $input = $driver === 'Other' ? "<input type='text' id='other_txt' value='{$survey->OtherBusinessDrivers}'>":'';
                    $text  = $driver === 'Other' ? 'Something else not listed here (please specify)': @DeploymentSurvey::$business_drivers_options[$driver];
                    if(!empty($text))
                          array_push($custom_field, new LiteralField('answer', '<li class="ui-state-default ui-sortable-handle" id="'.md5($driver).'_answer" data-key="'.md5($driver).'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$text.$input.'&nbsp;<a href="#" class="remove_answer" title="remove it">X</a></li>'));
                }
            }
        }
        array_push($custom_field, new LiteralField('options','</ol></div></div></div>'));
        array_push($custom_field, new HiddenField('BusinessDrivers'));
        array_push($custom_field, new HiddenField('OtherBusinessDrivers'));
        $fields = new FieldList (
            $custom_field
       );

        $answer_table = array();
        foreach(DeploymentSurvey::$business_drivers_options as $key => $value) {
            $hash = md5($key);
            $answer_table[$hash.'_answer'] = $key;
        }
        $answer_table['6311ae17c1ee52b36e68aaf4ad066387_answer'] = 'Other';
        $answer_table = json_encode($answer_table);


        Requirements::customScript('var answer_table = '.$answer_table.';');

        $fields->add(new CustomCheckboxSetField('InformationSources', 'Where do end up finding information about using OpenStack, after using search engines and talking to your colleagues?<BR>Select All That Apply', ArrayUtils::AlphaSort(DeploymentSurvey::$information_options, null, array('Other' => 'Other Sources (please specify)'))));
        $fields->add(new TextAreaField('OtherInformationSources', ''));
        $fields->add($ddl_rate = new DropdownField(
            'OpenStackRecommendRate',
            'How likely are you to recommend OpenStack to a friend or colleague? (0=Least Likely, 10=Most Likely)',
            DeploymentSurvey::$openstack_recommendation_rate_options));
        $fields->add(new LiteralField('Break', '<hr/>'));
        $fields->add(new LiteralField('Break', '<p>We would love to hear how OpenStack and the OpenStack Foundation can better meet your needs. These free-form questions are optional, but will provide valuable insights.</p>'));
        $fields->add(new LiteralField('Break', '<p>Your responses are anonymous, and each of these text fields is independent, so we cannot “See previous answer”. We would really appreciate a separate answer to each question.</p>'));
        $fields->add(new TextAreaField('WhatDoYouLikeMost', 'What do you like most about OpenStack, besides “free” and “open”?'));
        $fields->add(new TextAreaField('FurtherEnhancement', 'What areas of OpenStack require further enhancement? '));
        $fields->add(new TextAreaField('FoundationUserCommitteePriorities', 'What should be the priorities for the Foundation and User Committee during the coming year?'));
        $ddl_rate->setEmptyString('Neutral');
        $actions = new FieldList(
            $nextButton
        );

        $validator = new RequiredFields();
        Requirements::javascript('surveys/js/deployment_survey_yourthoughts_form.js');

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    function forTemplate(){
        return $this->renderWith(array(
            $this->class,
            'Form'
        ));
    }

} 