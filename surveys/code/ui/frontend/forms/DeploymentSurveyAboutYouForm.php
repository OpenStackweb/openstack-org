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

/**
 * Class DeploymentSurveyAboutYouForm
 */
class DeploymentSurveyAboutYouForm extends Form {

    private $first_name_field;
    private $last_name_field;
    private $email_field;

    function __construct($controller, $name){

        $current_user = Member::currentUser();

        $fields = new FieldList (
            $this->first_name_field = new TextField('FirstName', 'First name / Given name', $current_user->FirstName),
            $this->last_name_field  = new TextField('Surname', 'Last name / Family name', $current_user->Surname),
            $os_activity            = new CustomCheckboxSetField('OpenStackActivity', 'Which of the following do you yourself personally do?<BR>Select All That Apply', DeploymentSurveyOptions::$activities_options),
            $os_relationship        = new TextAreaField('OpenStackRelationship', 'Please describe your relationship with OpenStack'),
            $this->email_field      = new TextField('Email', 'Your Email', $current_user->Email),
            $ok_2_contact           = new CheckboxField('OkToContact', 'The OpenStack Foundation and User Committee may communicate with me in the future about my usage.')
        );

        $os_relationship->addExtraClass('hidden');
        $os_relationship->setColumns(30);
        $ok_2_contact->setValue(0);
        $this->email_field->setDisabled(true);
        $nextButton = new FormAction('NextStep', '  Next Step  ');

        $actions = new FieldList(
            $nextButton
        );

        $validator = new RequiredFields();

        Requirements::javascript('surveys/js/deployment_survey_aboutyou_form.js');

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    function forTemplate(){
        return $this->renderWith(array(
            $this->class,
            'Form'
        ));
    }

    function loadDataFrom($data, $clearMissingFields = false, $fieldList = null)
    {
        $res = parent::loadDataFrom($data, $clearMissingFields, $fieldList);
        $current_user = Member::currentUser();
        if ($data instanceof DeploymentSurvey) {
            if (empty($data->FirstName)) {
                $this->first_name_field->setValue($current_user->FirstName);
            }
            if (empty($data->Surname)) {
                $this->last_name_field->setValue($current_user->Surname);
            }
            if (empty($data->Email)) {
                $this->email_field->setValue($current_user->Email);
            }
        }
        return $res;
    }
}