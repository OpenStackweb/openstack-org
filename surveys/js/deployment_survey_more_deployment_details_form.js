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

jQuery(document).ready(function($) {

    var form  = $('#DeploymentSurveyMoreDeploymentDetailsForm_Form');
    var form_validator = null;

    form_validator = form.validate({
        rules: {
            'OperatingSystems': {required : true},
            'UsedPackages[]':  {required : true},
            'DeploymentTools[]':{required : true},
            'PaasTools': {required : true},
            'SupportedFeatures[]': {required : true},
            'UsedDBForOpenStackComponents[]': {required : true},
            'InteractingClouds[]': {required : true},
            'NumCloudUsers': {required : true},
            'ComputeNodes': {required : true},
            'ComputeCores': {required : true},
            'ComputeInstances': {required : true},
            'NetworkNumIPs': {required : true},
            'BlockStorageTotalSize': {required : true},
            'ObjectStorageSize': {required : true},
            'ObjectStorageNumObjects': {required : true},
            'SwiftGlobalDistributionFeatures': {required : true},
            'ToolsUsedForYourUsers': {required : true}
        },
        ignore: [],
        invalidHandler: jqueryValidatorInvalidHandler,
        errorPlacement: jqueryValidatorErrorPlacement
    });

    var fields = [];
    fields.push($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_UsedPackages_Packagesyouvemodified'));
    fields.push($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_UsedPackages_Packagesyouvebuiltyourself'));

    setCustomValidationRuleForDependantQuestion(fields, $('#custom_package_reason_container'), function(chk){
        if(chk.is(':checked')){
            $('input[type="checkbox"]','#DeploymentSurveyMoreDeploymentDetailsForm_Form_CustomPackagesReason').rules('add', { required:true});
        }
        else{
            $('input[type="checkbox"]','#DeploymentSurveyMoreDeploymentDetailsForm_Form_CustomPackagesReason').rules('remove', 'required');
        }
    });

    setCustomValidationRuleForOtherText($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_CustomPackagesReason_Other'), $('#OtherCustomPackagesReason'));
    setCustomValidationRuleForOtherTextDropdown($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_OperatingSystems'),$('#OtherOperatingSystems'));
    setCustomValidationRuleForOtherText($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_DeploymentTools_OtherTool'), $('#OtherDeploymentTools'));

    setCustomValidationRuleForOtherTextDropdown($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_PaasTools'), $('#OtherPaasTools'));

    setCustomValidationRuleForOtherText($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_SupportedFeatures_OtherCompatibilityAPI'), $('#OtherSupportedFeatures'));

    setCustomValidationRuleForOtherText($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_UsedDBForOpenStackComponents_OtherDatabase'), $('#OtherUsedDBForOpenStackComponents'));

    if($('#Hypervisors').length > 0) {
        $('.checkbox', $('#Hypervisors')).rules('add', { required: true} );
        setCustomValidationRuleForOtherText($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_Hypervisors_OtherHypervisors'), $('#OtherHypervisor'));
    }

    if($('#NetworkDrivers').length > 0) {
        $('.checkbox', $('#NetworkDrivers')).rules('add', { required: true} );
        setCustomValidationRuleForOtherText($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_NetworkDrivers_OtherNetworkDriver'), $('#OtherNetworkDriver'));
    }

    if($('#IdentityDrivers').length > 0) {
        $('.checkbox', $('#IdentityDrivers')).rules('add', { required: true} );
        setCustomValidationRuleForOtherText($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_IdentityDrivers_OtherIdentityDriver'), $('#OtherIndentityDriver'));
    }

    if($('#BlockStorageDrivers').length > 0) {
        $('.checkbox', $('#BlockStorageDrivers')).rules('add', { required: true} );
        setCustomValidationRuleForOtherText($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_BlockStorageDrivers_OtherBlockStorageDriver'), $('#OtherBlockStorageDriver'));
    }

    setCustomValidationRuleForOtherText($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_InteractingClouds_OtherClouds'), $('#OtherInteractingClouds'));

    if($('#WhyNovaNetwork').length > 0) {
        $('.checkbox', $('#WhyNovaNetwork')).rules('add', { required: true} );
        setCustomValidationRuleForOtherText($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_WhyNovaNetwork_OtherReason'), $('#OtherWhyNovaNetwork'));
    }

    if($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_SwiftGlobalDistributionFeatures').length > 0){
        $('#DeploymentSurveyMoreDeploymentDetailsForm_Form_SwiftGlobalDistributionFeatures').rules('add', { required: true} );
        $('#DeploymentSurveyMoreDeploymentDetailsForm_Form_Plans2UseSwiftStoragePolicies').rules('add', { required: true} );
        $('#DeploymentSurveyMoreDeploymentDetailsForm_Form_SwiftGlobalDistributionFeaturesUsesCases').rules('add', { required: true} );

        setCustomValidationRuleForOtherTextDropdown($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_Plans2UseSwiftStoragePolicies'), $('#OtherPlans2UseSwiftStoragePolicies'), 'Maybe. Please explain');
        setCustomValidationRuleForOtherTextDropdown($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_SwiftGlobalDistributionFeaturesUsesCases'), $('#OtherSwiftGlobalDistributionFeaturesUsesCases'));
    }

    setCustomValidationRuleForOtherTextDropdown($('#DeploymentSurveyMoreDeploymentDetailsForm_Form_ToolsUsedForYourUsers'), $('#OtherToolsUsedForYourUsers'));

    setStep('deployments');
});