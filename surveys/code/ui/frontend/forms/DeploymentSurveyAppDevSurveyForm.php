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
class DeploymentSurveyAppDevSurveyForm extends Form
{

    function __construct($controller, $name)
    {

        // Define fields //////////////////////////////////////

        $fields = new FieldList (
            new LiteralField('Break', ColumnFormatter::$left_column_start),
            new CheckboxSetField(
                'Toolkits',
                'What toolkits do you use or plan to use to interact with the OpenStack API?<BR>Select All That Apply',
                ArrayUtils::AlphaSort(AppDevSurvey::$toolkits_options, null, array('Other' => 'Other Toolkits (please specify)'))),
            new TextField('OtherToolkits', 'Other toolkits'),

            new LiteralField('Break', ColumnFormatter::$right_column_start),
            new CheckboxSetField('ProgrammingLanguages',
                'If you wrote your own code for interacting with the OpenStack API, what programming language did you write it in?',
                ArrayUtils::AlphaSort(AppDevSurvey::$languages_options, null, array('Other' => 'Other (please specify)'))),
            new TextField('OtherProgrammingLanguages', 'Other programming languages'),

            new LiteralField('Break', ColumnFormatter::$end_columns),

            new LiteralField('Break', ColumnFormatter::$left_column_start),
            new CheckboxSetField('APIFormats',
                'If you wrote your own code for interacting with the OpenStack API, what wire format are you using?<BR>Select All That Apply',
                ArrayUtils::AlphaSort(AppDevSurvey::$api_format_options), null, array('Other' => 'Other Wire Format (please specify)')),
            new TextField('OtherAPIFormats', 'Other Wire Format'),
            new CheckboxSetField(
                'OperatingSystems',
                'What operating systems are you using or plan on using to develop your applications?<BR>Select All That Apply',
                ArrayUtils::AlphaSort(AppDevSurvey::$opsys_options, null, array('Other' => 'Other Development OS (please specify)'))),
            new TextField('OtherOperatingSystems', 'Other Development OS'),
            new CheckboxSetField(
                'GuestOperatingSystems',
                'What guest operating systems are you using or plan on using to deploy your applications to customers?<BR>Select All That Apply',ArrayUtils::AlphaSort(AppDevSurvey::$opsys_options, null, array('Other' => 'Other Development OS (please specify)'))),
            new TextField('OtherGuestOperatingSystems', 'Other Development OS'),
            new LiteralField('Break', '<hr/>'),
            new LiteralField('Break', '<p>Please share your thoughts with us on the state of applications on OpenStack</p>'),
            new TextAreaField('StruggleDevelopmentDeploying', 'What do you struggle with when developing or deploying applications on OpenStack?'),
            $docs = new DropdownField(
                'DocsPriority',
                'What is your top priority in evaluating API and SDK docs?', AppDevSurvey::$docs_priority_options),
            new TextField('OtherDocsPriority', 'Other')
        );



        // $prevButton = new CancelFormAction($controller->Link().'Login', 'Previous Step');
        $nextButton = new FormAction('SaveAppDevSurvey', '  Next Step  ');

        $actions = new FieldList(
            $nextButton
        );

        // Create Validators
        $validator = new RequiredFields();
        Requirements::javascript('surveys/js/deployment_survey_appdevsurvey_form.js');
        parent::__construct($controller, $name, $fields, $actions, $validator);

        if ($AppDevSurvey = $this->controller->LoadAppDevSurvey()) {
            $this->loadDataFrom($AppDevSurvey->data());
        }
    }


    public function SaveAppDevSurvey($data, $form)
    {
        $survey = $form->controller->GetCurrentSurvey();
        $AppDevSurvey = $form->controller->LoadAppDevSurvey();

        // If a deployment wasn't returned, we'll create a new one
        if (!$AppDevSurvey) {
            $AppDevSurvey = new AppDevSurvey();
            $AppDevSurvey->MemberID = Member::currentUser()->ID;
            $AppDevSurvey->DeploymentSurveyID = $survey->ID;
        }

        $form->saveInto($AppDevSurvey);
        $AppDevSurvey->write();

        $survey->CurrentStep = 'Deployments';
        $survey->HighestStepAllowed = 'Deployments';
        $survey->UpdateDate = SS_Datetime::now()->Rfc2822();
        $survey->write();

        Controller::curr()->redirect($form->controller->Link() . 'Deployments');
    }

    function forTemplate()
    {
        return $this->renderWith(array(
            $this->class,
            'Form'
        ));
    }

}
