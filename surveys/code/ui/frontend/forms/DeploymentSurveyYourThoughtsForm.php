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
            new LiteralField('p1','<p>What are your top business drivers for using OpenStack?</p>'),
            new LiteralField('p2','<p>Please rank up to 5.</p>'),
            new LiteralField('p3','<p>1 = top business driver, 2 = next, 3 = third, and so on</p>'),
            new LiteralField('p3','<p><strong>Click your options to rank them. Select at least one.</strong>&nbsp;&nbsp;<a title="clear all options." href="#" id="clear_all_business_drivers">clear all</a></p>'),
            new LiteralField('table_begin','<table class="your-thoughts-table"><tbody>'),
        );

        $business_drivers = explode(',',$survey->BusinessDrivers);
        $business_drivers = array_combine($business_drivers,$business_drivers);

        foreach(DeploymentSurvey::$business_drivers_options as $key => $value){
            $hash = md5($key);
            $input = '';

            if($key == 'Other'){
                $val = '';
                if(isset($business_drivers[$key]))
                    $val = $survey->OtherBusinessDrivers;

                $input = sprintf('&nbsp;<input type="text" id="business_drivers_other_text" value="%s"/>',$val);
            }
            $index = false;
            if(isset($business_drivers[$key])){
                $index = array_search($key,array_keys($business_drivers));
            }

            if($index === false){
                $row_html = sprintf('<tr><td class="rank-wrapper" data-answer="%s_answer"></td><td class="rank-text">%s%s</td></tr>', $hash, $value, $input);
            }
            else {

                ++$index;
                $row_html = sprintf('<tr><td class="rank-wrapper selected-rank" data-answer="%s_answer" data-sort="%s">%s</td><td class="rank-text">%s%s</td></tr>', $hash, $index, $index, $value, $input);
            }
            array_push($custom_field,new LiteralField('row',$row_html));
            array_push($custom_field,new LiteralField('spacer','<tr class="spacer"></tr>'));
        }
        array_push($custom_field,new LiteralField('end_table','</tbody></table>'));
        array_push($custom_field, new HiddenField('BusinessDrivers'));
        array_push($custom_field, new HiddenField('OtherBusinessDrivers'));
        array_push($custom_field,new LiteralField('footer','</div>'));
        $fields = new FieldList (
            $custom_field
       );


        $answer_table = array();
        foreach(DeploymentSurvey::$business_drivers_options as $key => $value) {
            $hash = md5($key);
            $answer_table[$hash.'_answer'] = $key;
        }
        $answer_table = json_encode($answer_table);


        Requirements::customScript('var answer_table = '.$answer_table.';');
        Requirements::customScript('rank_order = '.count($business_drivers).';');

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
        $fields->add(new CheckboxField('InterestedUsingContainerTechnology','Are you interested in using container technology with OpenStack?'));
        $fields->add(new LiteralField('Break', '<div id="container_related_tech" class="hidden">'));
        $fields->add(new CustomCheckboxSetField('ContainerRelatedTechnologies','Which of the following container related technologies are you interested in using?<BR>Select All That Apply', DeploymentSurvey::$container_related_technologies));
        $fields->add(new LiteralField('Break', '</div>'));

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