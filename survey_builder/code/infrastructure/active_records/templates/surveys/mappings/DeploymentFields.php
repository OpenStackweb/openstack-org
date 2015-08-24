<?php

/**
 * Copyright 2015 OpenStack Foundation
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
abstract class DeploymentFields
{
    public static $fields = array
    (
        'IsPublic' => 'IsPublic',
        'Label' => 'Label',
        'DeploymentStage' => 'DeploymentStage',
        'CountriesPhysicalLocation' => 'CountriesPhysicalLocation',
        'CountriesUsersLocation' => 'CountriesUsersLocation',
        'DeploymentType' => 'DeploymentType',
        'ProjectsUsed' => 'ProjectsUsed',
        'CurrentReleases' => 'CurrentReleases',
        'ServicesDeploymentsWorkloads' => 'ServicesDeploymentsWorkloads',
        'OtherServicesDeploymentsWorkloads' => 'OtherServicesDeploymentsWorkloads',
        'EnterpriseDeploymentsWorkloads' => 'EnterpriseDeploymentsWorkloads',
        'OtherEnterpriseDeploymentsWorkloads' => 'OtherEnterpriseDeploymentsWorkloads',
        'HorizontalWorkloadFrameworks' => 'HorizontalWorkloadFrameworks',
        'OtherHorizontalWorkloadFrameworks' => 'OtherHorizontalWorkloadFrameworks',
        'OperatingSystems'=>'OperatingSystems',
        'UsedPackages' => 'UsedPackages',
        'CustomPackagesReason' => 'CustomPackagesReason',
        'DeploymentTools' => 'DeploymentTools',
        'OtherDeploymentTools' => 'OtherDeploymentTools',
        'PaasTools' => 'PaasTools',
        'OtherPaasTools' => 'OtherPaasTools',
        'Hypervisors' => 'Hypervisors',
        'OtherHypervisor' => 'OtherHypervisor',
        'SupportedFeatures' => 'SupportedFeatures',
        'OtherSupportedFeatures' => 'OtherSupportedFeatures',
        'UsedDBForOpenStackComponents' => 'UsedDBForOpenStackComponents',
        'OtherUsedDBForOpenStackComponents' => 'OtherUsedDBForOpenStackComponents',
        'NetworkDrivers' => 'NetworkDrivers',
        'OtherNetworkDriver' => 'OtherNetworkDriver',
        'IdentityDrivers' => 'IdentityDrivers',
        'OtherIndentityDriver' => 'OtherIndentityDriver',
        'BlockStorageDrivers' => 'BlockStorageDrivers',
        'OtherBlockStorageDriver' => 'OtherBlockStorageDriver',
        'InteractingClouds' => 'InteractingClouds',
        'OtherInteractingClouds' => 'OtherInteractingClouds',
        'NumCloudUsers' => 'NumCloudUsers',
        'ComputeNodes' => 'ComputeNodes',
        'ComputeCores' => 'ComputeCores',
        'ComputeInstances' => 'ComputeInstances',
        'NetworkNumIPs' => 'NetworkNumIPs',
        'BlockStorageTotalSize' => 'BlockStorageTotalSize',
        'ObjectStorageSize' => 'ObjectStorageSize',
        'ObjectStorageNumObjects' => 'ObjectStorageNumObjects',
        'WhyNovaNetwork' => 'WhyNovaNetwork',
        'OtherWhyNovaNetwork' => 'OtherWhyNovaNetwork',
        'SwiftGlobalDistributionFeatures' => 'SwiftGlobalDistributionFeatures',
        'SwiftGlobalDistributionFeaturesUsesCases' => 'SwiftGlobalDistributionFeaturesUsesCases',
        'OtherSwiftGlobalDistributionFeaturesUsesCases' => 'OtherSwiftGlobalDistributionFeaturesUsesCases',
        'Plans2UseSwiftStoragePolicies' => 'Plans2UseSwiftStoragePolicies',
        'OtherPlans2UseSwiftStoragePolicies' => 'OtherPlans2UseSwiftStoragePolicies',
        'ToolsUsedForYourUsers' => 'ToolsUsedForYourUsers',
        'OtherToolsUsedForYourUsers' => 'OtherToolsUsedForYourUsers',
        'Reason2Move2Ceilometer' => 'Reason2Move2Ceilometer'
    );

    /**
     * @return array
     */
    public static function toArray()
    {
        return array_keys(self::$fields);
    }
}