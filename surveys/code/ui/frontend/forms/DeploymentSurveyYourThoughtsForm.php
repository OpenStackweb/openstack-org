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

        $nextButton = new FormAction('NextStep', '  Next Step  ');
        //BusinessDrivers

        $custom_field = array(
            new LiteralField('header','<div id="catalog">'),
            new LiteralField('custom_question', 'What are your top business drivers for using OpenStack?<BR>Please rank up to 5.<BR>1 = top business driver, 2 = next, 3 = third, and so on<BR>Select At Least One'),
            new LiteralField('ul_begin','<ul>'),
        );

        foreach(DeploymentSurvey::$business_drivers_options as $key => $value){
            array_push($custom_field,new LiteralField('li_option','<li data-key="'.$key.'" class="answer_option" id="'.$key.'">'.$value.'</li>'));
        }
        array_push($custom_field, new LiteralField('li_option','<li data-key="Other" class="answer_option" id="Other">Something else not listed here (please specify)</li>'));
        array_push($custom_field, new LiteralField('ul_end','</ul>'));

        array_push($custom_field, new LiteralField('options','<div id="options"><div class="ui-widget-content"><ol><li class="placeholder">Add your answers here</li></ol></div></div>'));
        array_push($custom_field, new HiddenField('BusinessDrivers'));
        array_push($custom_field, new HiddenField('OtherBusinessDrivers'));
        $fields = new FieldList (
            $custom_field
       );


        $fields->add(new CustomCheckboxSetField('InformationSources', 'Where do end up finding information about using OpenStack, after using search engines and talking to your colleagues?<BR>Select All That Apply', ArrayUtils::AlphaSort(DeploymentSurvey::$information_options, null, array('Other' => 'Other Sources (please specify)'))));
        $fields->add(new TextAreaField('OtherInformationSources', ''));
        $fields->add(new DropdownField(
            'OpenStackRecommendRate',
            'How likely are you to recommend OpenStack to a friend or colleague? (0=Least Likely, 10=Most Likely)',
            DeploymentSurvey::$openstack_recommendation_rate_options));
        $fields->add(new LiteralField('Break', '<hr/>'));
        $fields->add(new LiteralField('Break', '<p>We would love to hear how OpenStack and the OpenStack Foundation can better meet your needs. These free-form questions are optional, but will provide valuable insights.</p>'));
        $fields->add(new LiteralField('Break', '<p>Your responses are anonymous, and each of these text fields is independent, so we cannot “See previous answer”. We would really appreciate a separate answer to each question.</p>'));
        $fields->add(new TextAreaField('WhatDoYouLikeMost', 'What do you like most about OpenStack, besides “free” and “open”?'));
        $fields->add(new TextAreaField('FurtherEnhancement', 'What areas of OpenStack require further enhancement? '));
        $fields->add(new TextAreaField('FoundationUserCommitteePriorities', 'What should be the priorities for the Foundation and User Committee during the coming year?'));

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