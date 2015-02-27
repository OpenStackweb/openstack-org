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
class Deployment extends DataObject
{

    private static $db = array(

        // Section 2
        'Label' => 'Text',
        'IsPublic' => 'Boolean',
        'DeploymentType' => 'Text',
        'ProjectsUsed' => 'Text',
        'CurrentReleases' => 'Text',
        'DeploymentStage' => 'Text',
        'NumCloudUsers' => 'Text',
        'WorkloadsDescription' => 'Text',
        'OtherWorkloadsDescription' => 'Text',

        // Section 3
        'APIFormats' => "Text",
        'Hypervisors' => "Text",
        'OtherHypervisor' => 'Text',
        'BlockStorageDrivers' => 'Text',
        'OtherBlockStorageDriver' => 'Text',
        'NetworkDrivers' => 'Text',
        'OtherNetworkDriver' => 'Text',
        'WhyNovaNetwork' => 'Text',
        'OtherWhyNovaNetwork' => 'Text',
        'IdentityDrivers' => 'Text',
        'OtherIndentityDriver' => 'Text',
        'SupportedFeatures' => 'Text',
        'DeploymentTools' => 'Text',
        'OtherDeploymentTools' => 'Text',
        'OperatingSystems' => 'Text',
        'OtherOperatingSystems' => 'Text',
        'ComputeNodes' => 'Text',
        'ComputeCores' => 'Text',
        'ComputeInstances' => 'Text',
        'BlockStorageTotalSize' => 'Text',
        'ObjectStorageSize' => 'Text',
        'ObjectStorageNumObjects' => 'Text',
        'NetworkNumIPs' => 'Text',
        //control fields
        'SendDigest' => 'Boolean',// SendDigest = 1 SENT, SendDigest = 0 to be send
        'UpdateDate' => 'SS_Datetime',
        'SwiftGlobalDistributionFeatures' => 'Text',
        'SwiftGlobalDistributionFeaturesUsesCases' => 'Text',
        'OtherSwiftGlobalDistributionFeaturesUsesCases' => 'Text',
        'Plans2UseSwiftStoragePolicies' => 'Text',
        'OtherPlans2UseSwiftStoragePolicies' => 'Text',
        'UsedDBForOpenStackComponents' => 'Text',
        'OtherUsedDBForOpenStackComponents' => 'Text',
        'ToolsUsedForYourUsers' => 'Text',
        'OtherToolsUsedForYourUsers' => 'Text',
        'Reason2Move2Ceilometer' => 'Text',
        'CountriesPhysicalLocation' => 'Text',
        'CountriesUsersLocation' => 'Text',
        'ServicesDeploymentsWorkloads' => 'Text',
        'OtherServicesDeploymentsWorkloads' => 'Text',
        'EnterpriseDeploymentsWorkloads' => 'Text',
        'OtherEnterpriseDeploymentsWorkloads' => 'Text',
        'HorizontalWorkloadFrameworks' => 'Text',
        'OtherHorizontalWorkloadFrameworks' => 'Text',
        'UsedPackages' => 'Text',
        'CustomPackagesReason' => 'Text',
        'OtherCustomPackagesReason' => 'Text',
        'PaasTools' => 'Text',
        'OtherPaasTools' => 'Text',
        'OtherSupportedFeatures' => 'Text',
        'InteractingClouds' => 'Text',
        'OtherInteractingClouds' => 'Text',
    );

    private static $defaults = array(
    );

    private static $has_one = array(
        'DeploymentSurvey' => 'DeploymentSurvey',
        'Org' => 'Org'
    );

    private static $summary_fields = array(
        'Created' => 'Created',
        'Label' => 'Label',
        'Org.Name' => 'Organization',
        'DeploymentSurvey.Member.Email' => 'Creator',
    );

    static $searchable_fields = array(
        'Created' => 'ExactMatchFilter',
        'Org.Name' => 'PartialMatchFilter',
        'DeploymentSurvey.Member.Email'=> 'PartialMatchFilter',
    );

    private static $singular_name = 'Deployment';
    private static $plural_name = 'Deployments';

    function getCMSFields()
    {
        $CountryCodes = CountryCodes::$iso_3166_countryCodes;

        $fields = new FieldList(
            $rootTab = new TabSet("Root")
        );

        $fields->addFieldsToTab('Root.Main',
            array(
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
                new CustomCheckboxSetField('ProjectsUsed', 'Projects Used', DeploymentOptions::$projects_used_options),
                new CustomCheckboxSetField('CurrentReleases', 'What releases are you currently using?', DeploymentOptions::$current_release_options),
                new LiteralField('Break','Describe the workloads and frameworks running in this OpenStack environment.<BR>Select All That Apply'),
                new LiteralField('Break','<hr/>'),
                new CustomCheckboxSetField('ServicesDeploymentsWorkloads','<b>Services Deployments - workloads designed to be accessible for external users / customers</b>',DeploymentOptions::$services_deployment_workloads_options),
                $other_service_workload = new TextAreaField('OtherServicesDeploymentsWorkloads',''),
                new CustomCheckboxSetField('EnterpriseDeploymentsWorkloads','<b>Enterprise Deployments - workloads designed to be run internally to support business</b>', DeploymentOptions::$enterprise_deployment_workloads_options),
                $other_enterprise_workload = new TextAreaField('OtherEnterpriseDeploymentsWorkloads',''),
                new CustomCheckboxSetField('HorizontalWorkloadFrameworks','<b>Horizontal Workload Frameworks</b>', DeploymentOptions::$horizontal_workload_framework_options),
                $other_horizontal_workload = new TextAreaField('OtherHorizontalWorkloadFrameworks','')
            ));

        $ddl_type->setEmptyString('-- Select One --');
        $ddl_stage->setEmptyString('-- Select One --');

        $details =   array(
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

            new CustomCheckboxSetField(
                'CustomPackagesReason',
                'If you have modified packages or have built your own packages, why?<BR>Select All That Apply', DeploymentOptions::$custom_package_reason_options
            ),
            $other_custom_reason = new TextareaField('OtherCustomPackagesReason', ''),

            new CustomCheckboxSetField('DeploymentTools','What tools are you using to deploy / configure this cluster?<BR>Select All That Apply', DeploymentOptions::$deployment_tools_options),
            $other_deployment_tools = new TextareaField('OtherDeploymentTools',''),
            new DropdownField(
                'PaasTools',
                'What Platform-as-a-Service (PaaS) tools are you using to manage applications on this OpenStack deployment?',
                ArrayUtils::AlphaSort(DeploymentOptions::$paas_tools_options, array('' => '-- Select One --'), array('Other' => 'Other Tool (please specify)'))
            ),
            $other_paas = new TextareaField('OtherPaasTools', ''),
            new CustomCheckboxSetField(
                'Hypervisors',
                'If this deployment uses <b>OpenStack Compute (Nova)</b>, which hypervisors are you using?<BR>Select All That Apply', DeploymentOptions::$hypervisors_options
            ),
            new TextareaField('OtherHypervisor', ''),
            new CustomCheckboxSetField(
                'SupportedFeatures',
                'Which compatibility APIs does this deployment support?<BR> Select All That Apply',
                DeploymentOptions::$deployment_features_options
            ),
            new TextareaField('OtherSupportedFeatures', ''),
            new CustomCheckboxSetField(
                'UsedDBForOpenStackComponents',
                'What database do you use for the components of this OpenStack cloud?<BR>Select All That Apply',
                DeploymentOptions::$used_db_for_openstack_components_options
            ),
            new TextareaField('OtherUsedDBForOpenStackComponents', ''),
            new CustomCheckboxSetField(
                'NetworkDrivers',
                ' If this deployment uses <b>OpenStack Network (Neutron)</b>, which drivers are you using?<BR>Select All That Apply', DeploymentOptions::$network_driver_options
            ),
            new TextareaField('OtherNetworkDriver', ''),
            new CustomCheckboxSetField(
                'IdentityDrivers',
                'If you are using <b>OpenStack Identity Service (Keystone)</b> which OpenStack identity drivers are you using?<BR>Select All That Apply',
                DeploymentOptions::$identity_driver_options
            ),
            new TextareaField('OtherIndentityDriver', ''),
            new CustomCheckboxSetField(
                'BlockStorageDrivers',
                'If this deployment uses <b>OpenStack Block Storage (Cinder)</b>, which drivers are <BR>Select All That Apply',
                DeploymentOptions::$block_storage_divers_options
            ),
            new TextareaField('OtherBlockStorageDriver', ''),
            new CustomCheckboxSetField(
                'InteractingClouds',
                'With what other clouds does this OpenStack deployment interact?<BR>Select All That Apply',
                DeploymentOptions::$interacting_clouds_options
            ),
            new TextareaField('OtherInteractingClouds', ''),
            $ddl_users = new DropdownField(
                'NumCloudUsers',
                'Number of users',
                DeploymentOptions::$cloud_users_options
            ),
            $ddl_nodes = new DropdownField(
                'ComputeNodes',
                'Physical compute nodes',
                DeploymentOptions::$compute_nodes_options
            ),
            $ddl_cores = new DropdownField(
                'ComputeCores',
                'Processor cores',
                DeploymentOptions::$compute_cores_options
            ),
            $ddl_instances = new DropdownField(
                'ComputeInstances',
                'Number of instances',
                DeploymentOptions::$compute_instances_options
            ),
            $ddl_ips = new DropdownField(
                'NetworkNumIPs',
                'Number of fixed / floating IPs',
                DeploymentOptions::$network_ip_options
            ),
            $ddl_block_size = new DropdownField(
                'BlockStorageTotalSize',
                'If this deployment uses <b>OpenStack Block Storage (Cinder)</b>, what is the size of its block storage?',
                DeploymentOptions::$storage_size_options
            ),
            $ddl_block_size = new DropdownField(
                'ObjectStorageSize',
                'If this deployment uses <b>OpenStack Object Storage (Swift)</b>, what is the size of its block storage?',
                DeploymentOptions::$storage_size_options
            ),
            $ddl_objects_size = new DropdownField(
                'ObjectStorageNumObjects',
                'If this deployment uses <b>OpenStack Object Storage (Swift)</b>, how many total objects are stored?',
                DeploymentOptions::$storage_objects_options
            ),
            new LiteralField('Break', '<h3>Spotlight</h3>'),
            new LiteralField('Break', '<hr/>'),
            new CustomCheckboxSetField(
                'WhyNovaNetwork',
                'If this deployment uses nova-network and not OpenStack Network (Neutron), what would allow you to migrate to Neutron?',
                DeploymentOptions::$why_nova_network_options),
            $other_why_nova =  new TextareaField('OtherWhyNovaNetwork', ''),
            $ddl_swift_dist_feat = new DropdownField(
                'SwiftGlobalDistributionFeatures',
                'Are you using Swift\'s global distribution features?',
                DeploymentOptions::$swift_global_distribution_features_options),
            $ddl_uses_cases = new DropdownField(
                'SwiftGlobalDistributionFeaturesUsesCases',
                'If yes, what is your use case?',
                DeploymentOptions::$swift_global_distribution_features_uses_cases_options),
            $other_uses_cases = new TextareaField('OtherSwiftGlobalDistributionFeaturesUsesCases', ''),
            $ddl_policies = new DropdownField(
                'Plans2UseSwiftStoragePolicies',
                'Do you have plans to use Swift\'s storage policies or erasure codes in the next year?',
                DeploymentOptions::$plans_2_use_swift_storage_policies_options
            ),
            new TextareaField('OtherPlans2UseSwiftStoragePolicies', ''),
            $ddl_other_tools = new DropdownField(
                'ToolsUsedForYourUsers',
                'What tools are you using charging or show-back for your users?',
                DeploymentOptions::$tools_used_for_your_users_options
            ),
            $other_tools = new TextareaField('OtherToolsUsedForYourUsers', ''),
            new TextareaField('Reason2Move2Ceilometer', 'If you are not using Ceilometer, what would allow you to move to it (optional free text)?')
        );

        $ddl_users->setEmptyString('-- Select One --');

        $ddl_nodes->setEmptyString('-- Select One --');

        $ddl_cores->setEmptyString('-- Select One --');

        $ddl_instances->setEmptyString('-- Select One --');

        $ddl_ips->setEmptyString('-- Select One --');

        $ddl_block_size->setEmptyString('-- Select One --');

        $ddl_block_size->setEmptyString('-- Select One --');

        $ddl_objects_size->setEmptyString('-- Select One --');

        $ddl_swift_dist_feat->setEmptyString('-- Select One --');

        $ddl_uses_cases->setEmptyString('-- Select One --');

        $ddl_policies->setEmptyString('-- Select One --');

        $ddl_other_tools->setEmptyString('-- Select One --');

        $fields->addFieldsToTab('Root.Details', $details);
        return $fields;
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        // Create Validators
        $validator = new RequiredFields('Label',
            'IsPublic',
            'ProjectsUsed',
            'NumCloudUsers',
            'CurrentReleases',
            'DeploymentStage',
            'DeploymentType');
        return $validator;
    }

    public function getCountry()
    {
        return $this->DeploymentSurvey()->PrimaryCountry;
    }

    public function getIndustry()
    {
        return $this->DeploymentSurvey()->Industry;
    }

    public function getMember()
    {
        return $this->DeploymentSurvey()->Member();
    }

    public function OrgAndLabel()
    {
        return $this->getOrg() . ' - ' . $this->Label;
    }

    public function getOrg()
    {
        return $this->Org()->Name;
    }

    public function getUserStory()
    {
        if ($this->hasUserStory()) {
            return UserStory::get()->filter('DeploymentID', $this->ID)->first();
        }
        return false;
    }

    public function hasUserStory()
    {
        $userStory = UserStory::get()->filter('DeploymentID', $this->ID)->first();
        return ($userStory) ? true : false;
    }

    /**
     * @param int $batch_size
     * @return mixed
     */
    public function getNotDigestSent($batch_size)
    {
        Deployment::get()->filter('SendDigest', 0)->sort('UpdateDate', 'ASC')->limit($batch_size);
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->UpdateDate = SS_Datetime::now()->Rfc2822();
    }

    public function copyFrom(Deployment $oldDeployment){

        foreach(Deployment::$db as $field => $type){
            $value = $oldDeployment->getField($field);
            $this->setField($field, $value);
        }
    }

    public function getSurveyType(){
        $start_date = new DateTime(SURVEY_START_DATE);
        $created    = new DateTime($this->Created);
        if($created >= $start_date)
            return SurveyType::MARCH_2015;
        else
            return SurveyType::OLD;
    }

    public function canEdit($member = null) {
        return $this->getSurveyType() == SurveyType::MARCH_2015;
    }

    public function canDelete($member = null) {
        return $this->getSurveyType() == SurveyType::MARCH_2015;
    }

}