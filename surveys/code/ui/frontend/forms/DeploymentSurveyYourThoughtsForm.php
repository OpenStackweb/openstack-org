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

        $nextButton = new FormAction('NextStep', '  Next Step  ');

        $fields = new FieldList (
            new CheckboxSetField(
                'BusinessDrivers',
                'What are your top business drivers for using OpenStack?<BR>Please rank up to 5.<BR>1 = top business driver, 2 = next, 3 = third, and so on<BR>Select At Least One',
                ArrayUtils::AlphaSort(DeploymentSurvey::$business_drivers_options,null, array('Other' => 'Something else not listed here'))),
            new TextField('OtherBusinessDrivers', 'Other business drivers'),
            new CheckboxSetField('InformationSources', 'Where do end up finding information about using OpenStack, after using search engines and talking to your colleagues?<BR>Select All That Apply', ArrayUtils::AlphaSort(DeploymentSurvey::$information_options, null, array('Other' => 'Other Sources (please specify)'))),
            new TextField('OtherInformationSources', 'Other information sources'),
            new DropdownField(
                'OpenStackRecommendRate',
                'How likely are you to recommend OpenStack to a friend or colleague? (0=Least Likely, 10=Most Likely)',
                DeploymentSurvey::$openstack_recommendation_rate_options),
            new LiteralField('Break', '<hr/>'),
            new LiteralField('Break', '<p>We would love to hear how OpenStack and the OpenStack Foundation can better meet your needs. These free-form questions are optional, but will provide valuable insights.</p>'),
            new LiteralField('Break', '<p>Your responses are anonymous, and each of these text fields is independent, so we cannot “See previous answer”. We would really appreciate a separate answer to each question.</p>'),
            new TextAreaField('WhatDoYouLikeMost', 'What do you like most about OpenStack, besides “free” and “open”?'),
            new TextAreaField('FurtherEnhancement', 'What areas of OpenStack require further enhancement? '),
            new TextAreaField('FoundationUserCommitteePriorities', 'What should be the priorities for the Foundation and User Committee during the coming year?')
        );

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