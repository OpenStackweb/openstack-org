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
            'DeploymentTools[]':{required : true}
        },
        onfocusout: false,
        focusCleanup: true,
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
    setStep('deployments');
});