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
            new LiteralField('Break', '<p>The questions on this page are optional, but will help us better understand the details of how the OpenStack software is being used. Any information you provide on this step will be treated as private and confidential and only used in aggregate reporting.</p><hr>'),
            new LiteralField('Break', '<p>The information below will help us better understand the most common configuration and component choices OpenStack deployments are using.</p>'),
            new LiteralField('Break', '<h3>Telemetry</h3>'),
            new LiteralField('Break', '<hr/>'),
            new LiteralField('Break','<p>Please provide the following information about the size and scale of this OpenStack deployment. This information is optional, but will be kept confidential and <b>never</b> published in connection with you or your organization.</p>'),
            new DropdownField(
                'OperatingSystems',
                'What is the main operating system running this OpenStack cloud deployment?',
                ArrayUtils::AlphaSort(DeploymentOptions::$operating_systems_options, array('' => '-- Select One --'), array('Other' => 'Other (please specify)'))
            ),
            $other_os = new TextareaField('OtherOperatingSystems', ''),

            new CustomCheckboxSetField(
                'UsedPackages',
                'What packages does this deployment use…?<BR>Select All That Apply',
                DeploymentOptions::$used_packages_options
            ),
            new LiteralField('Container','<div id="custom_package_reason_container" class="hidden">'),
            new CustomCheckboxSetField(
                'CustomPackagesReason',
                ' If you have modified packages or have built your own packages, why?<BR>Select All That Apply', DeploymentOptions::$custom_package_reason_options
            ),
            $other_custom_reason = new TextareaField('OtherCustomPackagesReason', ''),
             new LiteralField('Container','</div>'),
             new CustomCheckboxSetField('DeploymentTools','What tools are you using to deploy / configure this cluster?<BR>Select All That Apply', DeploymentOptions::$deployment_tools_options),
             $other_deployment_tools = new TextareaField('OtherDeploymentTools',''),
            new CustomCheckboxSetField(
                'PaasTools',
                'What Platform-as-a-Service (PaaS) tools are you using to manage applications on this OpenStack deployment?',
                ArrayUtils::AlphaSort(DeploymentOptions::$paas_tools_options, array('' => '-- Select One --'), array('Other' => 'Other Tool (please specify)'))
            ),
            $other_paas = new TextareaField('OtherPaasTools', '')
        );

        $deployment = Controller::curr()->LoadDeployment($CurrentDeploymentID);

        if(is_null($deployment))  Controller::curr()->redirect(Controller::curr()->Link() . 'Deployments');

        $projects_used = $deployment->ProjectsUsed;

        if(strpos($projects_used, 'Openstack Compute (Nova)') !== false ){
            $fields->add(
                new CustomCheckboxSetField(
                    'Hypervisors',
                    'Which <b>OpenStack Compute (Nova)</b> hypervisors are you using?<BR>Select All That Apply', DeploymentOptions::$hypervisors_options
                )

            );
            $fields->add($other_hyper = new TextareaField('OtherHypervisor', ''));
            $other_hyper->addExtraClass('hidden');
        }

        $fields->add(
            new CustomCheckboxSetField(
                'SupportedFeatures',
                'Which compatibility APIs does this deployment support?<BR> Select All That Apply',
                DeploymentOptions::$deployment_features_options
            )
        );
        $fields->add($other_feat = new TextareaField('OtherSupportedFeatures', ''));

        $fields->add(
            new CustomCheckboxSetField(
                'UsedDBForOpenStackComponents',
                'What database do you use for the components of this OpenStack cloud?<BR>Select All That Apply',
                DeploymentOptions::$used_db_for_openstack_components_options
            )
        );
        $fields->add($other_db = new TextareaField('OtherUsedDBForOpenStackComponents', ''));

        if(strpos($projects_used, 'Openstack Network') !== false ){
            $fields->add(
                new CustomCheckboxSetField(
                    'NetworkDrivers',
                    ' Which <b>OpenStack Network (Neutron)</b> drivers are you using?<BR>Select All That Apply', DeploymentOptions::$network_driver_options
                )

            );
            $fields->add($other_driver = new TextareaField('OtherNetworkDriver', ''));
            $other_driver->addExtraClass('hidden');
        }

        if(strpos($projects_used, 'Openstack Identity Service (Keystone)') !== false ){
            $fields->add(new CustomCheckboxSetField(
                'IdentityDrivers',
                'Which  <b>OpenStack Identity Service (Keystone)</b> drivers are you using?<BR>Select All That Apply',
                DeploymentOptions::$identity_driver_options
            ));
            $fields->add($other_driver = new TextareaField('OtherIndentityDriver', ''));
            $other_driver->addExtraClass('hidden');
        }

        if(strpos($projects_used, 'Openstack Block Storage (Cinder)') !== false){
            $fields->add(new CustomCheckboxSetField(
                'BlockStorageDrivers',
                'Which <b>OpenStack Block Storage (Cinder)</b> drivers are you using?<BR>Select All That Apply',
                DeploymentOptions::$block_storage_divers_options
            ));
            $fields->add($other_driver= new TextareaField('OtherBlockStorageDriver', ''));
            $other_driver->addExtraClass('hidden');
        }

        $fields->add(new CustomCheckboxSetField(
            'InteractingClouds',
            'With what other clouds does this OpenStack deployment interact?<BR>Select All That Apply',
            DeploymentOptions::$interacting_clouds_options
        ));

        $fields->add($other_interacting_clouds= new TextareaField('OtherInteractingClouds', ''));
        $other_interacting_clouds->addExtraClass('hidden');

        $other_custom_reason->addExtraClass('hidden');
        $other_os->addExtraClass('hidden');
        $other_paas->addExtraClass('hidden');
        $other_feat->addExtraClass('hidden');
        $other_db->addExtraClass('hidden');
        $other_deployment_tools->addExtraClass('hidden');

        $fields->add(new LiteralField('Break', '<h3>What’s the size of this cloud…?</h3>'));
        $fields->add(new LiteralField('Break', '<hr/>'));

        $fields->add($ddl_users = new DropdownField(
            'NumCloudUsers',
            'Number of users',
             DeploymentOptions::$cloud_users_options
        ));

        $ddl_users->setEmptyString('-- Select One --');

        $fields->add($ddl_nodes = new DropdownField(
            'ComputeNodes',
            'Physical compute nodes',
            DeploymentOptions::$compute_nodes_options
        ));

        $ddl_nodes->setEmptyString('-- Select One --');


        $fields->add($ddl_cores = new DropdownField(
            'ComputeCores',
            'Processor cores',
            DeploymentOptions::$compute_cores_options
        ));

        $ddl_cores->setEmptyString('-- Select One --');

        $fields->add($ddl_instances = new DropdownField(
            'ComputeInstances',
            'Number of instances',
            DeploymentOptions::$compute_instances_options
        ));

        $ddl_instances->setEmptyString('-- Select One --');


        $fields->add($ddl_ips = new DropdownField(
            'NetworkNumIPs',
            'Number of fixed / floating IPs',
            DeploymentOptions::$network_ip_options
        ));

        $ddl_ips->setEmptyString('-- Select One --');


        if(strpos($projects_used, 'Openstack Block Storage (Cinder)') !== false){
            $fields->add($ddl_block_size = new DropdownField(
                'BlockStorageTotalSize',
                'Which <b>OpenStack Block Storage (Cinder)</b> size are you using?',
                DeploymentOptions::$storage_size_options
            ));

            $ddl_block_size->setEmptyString('-- Select One --');

        }


        if(strpos($projects_used, 'Openstack Object Storage (Swift)') !== false ){
            $fields->add($ddl_block_size = new DropdownField(
                'ObjectStorageSize',
                'Which <b>OpenStack Object Storage (Swift)</b> size are you using?',
                DeploymentOptions::$storage_size_options
            ));

            $ddl_block_size->setEmptyString('-- Select One --');

            $fields->add($ddl_objects_size = new DropdownField(
                'ObjectStorageNumObjects',
                'How many <b>OpenStack Object Storage (Swift)</b> objects are stored?',
                DeploymentOptions::$storage_objects_options
            ));

            $ddl_objects_size->setEmptyString('-- Select One --');
        }

        // SPOTLIGHT

        $fields->add(new LiteralField('Break', '<h3>Spotlight</h3>'));
        $fields->add(new LiteralField('Break', '<hr/>'));

        if(strpos($projects_used, 'Openstack Network') === false){
            $fields->add(
                new CustomCheckboxSetField(
                    'WhyNovaNetwork',
                    'If this deployment uses nova-network and not OpenStack Network (Neutron), what would allow you to migrate to Neutron?',
                   DeploymentOptions::$why_nova_network_options));

            $fields->add($other_why_nova =  new TextareaField('OtherWhyNovaNetwork', ''));
            $other_why_nova->addExtraClass('hidden');
        }


        if(strpos($projects_used, 'Openstack Object Storage (Swift)') !== false){
            $fields->add($ddl_swift_dist_feat = new DropdownField(
                'SwiftGlobalDistributionFeatures',
                'Are you using Swift\'s global distribution features?',
                DeploymentOptions::$swift_global_distribution_features_options)
            );

            $ddl_swift_dist_feat->setEmptyString('-- Select One --');

            $fields->add(
            $ddl_uses_cases = new DropdownField(
                'SwiftGlobalDistributionFeaturesUsesCases',
                'If yes, what is your use case?',
                DeploymentOptions::$swift_global_distribution_features_uses_cases_options));

            $ddl_uses_cases->setEmptyString('-- Select One --');
            $fields->add($other_uses_cases = new TextareaField('OtherSwiftGlobalDistributionFeaturesUsesCases', ''));

            $other_uses_cases->addExtraClass('hidden');


            $fields->add($ddl_policies = new DropdownField(
                'Plans2UseSwiftStoragePolicies',
                'Do you have plans to use Swift\'s storage policies or erasure codes in the next year?',
                DeploymentOptions::$plans_2_use_swift_storage_policies_options
            ));

            $ddl_policies->setEmptyString('-- Select One --');

            $fields->add($other_policies = new TextareaField('OtherPlans2UseSwiftStoragePolicies', ''));

            $other_policies->addExtraClass('hidden');
        }

        $fields->add($ddl_other_tools = new DropdownField(
            'ToolsUsedForYourUsers',
            'What tools are you using charging or show-back for your users?',
             DeploymentOptions::$tools_used_for_your_users_options
        ));

        $ddl_other_tools->setEmptyString('-- Select One --');

        $fields->add($other_tools = new TextareaField('OtherToolsUsedForYourUsers', ''));

        $other_tools->addExtraClass('hidden');

        $fields->add(new TextareaField('Reason2Move2Ceilometer', 'If you are not using Ceilometer, what would allow you to move to it (optional free text)?'));

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
        $deployment = $form->controller->LoadDeployment($id);

        $projects_used = $deployment->ProjectsUsed;
        if(strpos($projects_used, 'Openstack Network') === false){
            /*
             * [IF ABOVE <> “OPENSTACK NETWORK (NEUTRON)”, THEN DO NOT SHOW THIS QUESTION AND AUTOPOPULATE DATA WITH “nova-network”]
             */
            $deployment->NetworkDrivers = 'nova-network';

        }
        $form->saveInto($deployment);
        $deployment->write();

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

