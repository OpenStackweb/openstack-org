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
class DeploymentSurveyMoreDeploymentDetailsForm extends Form
{

    function __construct($controller, $name)
    {

        $CurrentDeploymentID = Session::get('CurrentDeploymentID');

        // Define fields //////////////////////////////////////

        $fields = new FieldList (
            new LiteralField('Break', '<p>The information below will help us better understand
        the most common configuration and component choices OpenStack deployments are using.</p>'),
            new LiteralField('Break', '<h3>Telemetry</h3>'),
            new LiteralField('Break', '<hr/>'),
            new LiteralField('Break','<p>Please provide the following information about the size and scale of this OpenStack deployment. This information is optional, but will be kept confidential and <b>never</b> published in connection with you or your organization.</p>'),
            new DropdownField(
                'OperatingSystems',
                'What is the main operating system running this OpenStack cloud deployment?',
                ArrayUtils::AlphaSort(Deployment::$operating_systems_options, array('' => '-- Select One --'), array('Other' => 'Other (please specify)'))
            ),
            $other_os = new TextareaField('OtherOperatingSystems', ''),

            new CustomCheckboxSetField(
                'UsedPackages',
                'What packages does this deployment use…?<BR>Select All That Apply',
                Deployment::$used_packages_options
            ),
            new LiteralField('Container','<div id="custom_package_reason_container" class="hidden">'),
            new CustomCheckboxSetField(
                'CustomPackagesReason',
                ' If you have modified packages or have built your own packages, why?<BR>Select All That Apply', Deployment::$custom_package_reason_options
            ),
            $other_custom_reason = new TextareaField('OtherCustomPackagesReason', ''),
             new LiteralField('Container','</div>'),
             new CustomCheckboxSetField('DeploymentTools','What tools are you using to deploy / configure this cluster?<BR>Select All That Apply', Deployment::$deployment_tools_options),
             $other_deployment_tools = new TextareaField('OtherDeploymentTools','')
        );


        $other_custom_reason->addExtraClass('hidden');
        $other_os->addExtraClass('hidden');
        $other_deployment_tools->addExtraClass('hidden');
        $saveButton = new FormAction('SaveDeployment', 'Save Deployment');
        $cancelButton = new CancelFormAction($controller->Link() . 'Deployments', 'Cancel');

        $actions = new FieldList(
            $saveButton, $cancelButton
        );

        // Create Validators
        $validator = new RequiredFields();


        parent::__construct($controller, $name, $fields, $actions, $validator);

        if ($CurrentDeploymentID) {
            //Populate the form with the current members data
            if ($Deployment = $this->controller->LoadDeployment($CurrentDeploymentID)) {
                $this->loadDataFrom($Deployment->data());
            } else {
                // HTTP ERROR
                return $this->httpError(403, 'Access Denied.');
            }
        }

        Requirements::javascript('surveys/js/deployment_survey_deployments_form.js');
        Requirements::javascript('surveys/js/deployment_survey_more_deployment_details_form.js');
    }

    function SaveDeployment($data, $form)
    {

        $id = Session::get('CurrentDeploymentID');

        // Only loaded if it belongs to current user
        $Deployment = $form->controller->LoadDeployment($id);

        $form->saveInto($Deployment);
        $Deployment->write();

        Session::clear('CurrentDeploymentID');
        Controller::curr()->redirect($form->controller->Link() . 'Deployments');
    }

    function Cancel($data, $form)
    {
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

