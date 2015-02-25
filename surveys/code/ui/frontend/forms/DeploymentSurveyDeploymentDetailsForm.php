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
class DeploymentSurveyDeploymentDetailsForm extends Form
{
    function __construct($controller, $name)
    {

        // Define fields //////////////////////////////////////

        $CurrentDeploymentID = Session::get('CurrentDeploymentID');
        $CountryCodes = CountryCodes::$iso_3166_countryCodes;
        $fields = new FieldList (
            new HiddenField('DeploymentID', 'DeploymentID', $CurrentDeploymentID),
            new LiteralField('Break', '<p>Each deployment profile can be marked public if you wish for the basic information on this page to appear on openstack.org. If you select private we will treat all of the profile information as confidential information.</p>'),
            new OptionSetField(
                'IsPublic',
                'Would you like to keep this information confidential or allow the Foundation to share information about this deployment publicly?',
                array('1' => '<strong>Willing to share:</strong> The information on this page may be shared for this deployment',
                    '0' => '<strong>Confidential:</strong> All details provided should be kept confidential to the OpenStack Foundation'),
                0
            ),
            new LiteralField('Break', '<hr/>'),
            new TextField('Label', 'Deployment Name<BR><p class="clean_text">Please create a friendly label, like “Production OpenStack Deployment”. This name is for your deployment in our survey tool. If several people at your organization work on one deployment, we would <b>really appreciate</b> you all referring to the same deployment by the same name!</p>'),
            $ddl_stage = new DropdownField(
                'DeploymentStage',
                'In what stage is your OpenStack deployment? (make a new deployment profile for each type of deployment)',
                DeploymentOptions::$stage_options
            ),
            new MultiDropdownField('CountriesPhysicalLocation','In which country / countries is this OpenStack deployment physically located?',$CountryCodes),
            new MultiDropdownField('CountriesUsersLocation','In which country / countries are the users / customers for this deployment physically located?',$CountryCodes),
            $ddl_type = new DropdownField('DeploymentType', 'Deployment Type', DeploymentOptions::$deployment_type_options),
            new CustomCheckboxSetField('ProjectsUsed', 'What projects does this deployment use?<BR>Select All That Apply', DeploymentOptions::$projects_used_options),
            new CustomCheckboxSetField('CurrentReleases', 'What releases does this deployment currently use?<BR>Select All That Apply', DeploymentOptions::$current_release_options),
            new LiteralField('Break','Describe the workloads and frameworks running in this OpenStack environment.<BR>Select All That Apply'),
            new LiteralField('Break','<hr/>'),
            new CustomCheckboxSetField('ServicesDeploymentsWorkloads','<b>Services Deployments - workloads designed to be accessible for external users / customers</b>',DeploymentOptions::$services_deployment_workloads_options),
            $other_service_workload = new TextAreaField('OtherServicesDeploymentsWorkloads',''),
            new CustomCheckboxSetField('EnterpriseDeploymentsWorkloads','<b>Enterprise Deployments - workloads designed to be run internally to support business</b>', DeploymentOptions::$enterprise_deployment_workloads_options),
            $other_enterprise_workload = new TextAreaField('OtherEnterpriseDeploymentsWorkloads',''),
            new CustomCheckboxSetField('HorizontalWorkloadFrameworks','<b>Horizontal Workload Frameworks</b>', DeploymentOptions::$horizontal_workload_framework_options),
            $other_horizontal_workload = new TextAreaField('OtherHorizontalWorkloadFrameworks','')
        );

        $saveButton = new FormAction('SaveDeployment', 'Next Step');
        $nextButton = new CancelFormAction($controller->Link() . 'Deployments', 'Cancel');

        $other_service_workload->addExtraClass('hidden');
        $other_enterprise_workload->addExtraClass('hidden');
        $other_horizontal_workload->addExtraClass('hidden');
        $ddl_type->setEmptyString('-- Select One --');
        $ddl_stage->setEmptyString('-- Select One --');

        $actions = new FieldList(
            $saveButton, $nextButton
        );

        // Create Validators
        $validator = null;

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

        Requirements::javascript('surveys/js/deployment_survey_deployment_details_form.js');

    }

    function SaveDeployment($data, $form)
    {

        $id = convert::raw2sql($data['DeploymentID']);

        // Only loaded if it belongs to current user
        $Deployment = $form->controller->LoadDeployment($id);

        // If a deployment wasn't returned, we'll create a new one
        if (!$Deployment) {
            $Deployment = new Deployment();
            $Deployment->OrgID = Member::currentUser()->getCurrentOrganization()->ID;
            $newDeploy = true;
        }

        $form->saveInto($Deployment);


        $survey = $form->controller->GetCurrentSurvey();
        $Deployment->DeploymentSurveyID = $survey->ID;
        $Deployment->UpdateDate = SS_Datetime::now()->Rfc2822();
        $Deployment->OrgID = $survey->OrgID;
        $Deployment->write();
        /**/

        $survey->CurrentStep = 'MoreDeploymentDetails';
        $survey->HighestStepAllowed = 'MoreDeploymentDetails';
        $survey->UpdateDate = SS_Datetime::now()->Rfc2822();
        $survey->write();


        // If it is a new deployment and it is public, we send an email...
        if (isset($newDeploy) && $Deployment->IsPublic === 1) {

            global $email_new_deployment;
            global $email_from;

            $email = EmailFactory::getInstance()->buildEmail($email_from,
                $email_new_deployment,
                'New Deployment');

            $email->setTemplate('NewDeploymentEmail');

            $email->populateTemplate(array(
                'Deployment' => $Deployment,
            ));

            $email->send();
        }

        Session::set('CurrentDeploymentID', $Deployment->ID);
        Controller::curr()->redirect($form->controller->Link() . 'MoreDeploymentDetails');
    }

    function forTemplate()
    {
        return $this->renderWith(array(
            $this->class,
            'Form'
        ));
    }

}
