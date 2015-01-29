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
(function ($) {

    $(document).ready(function () {

        var form  = $('#DeploymentSurveyDeploymentDetailsForm_Form');
        var form_validator = null;

        form_validator = form.validate({
            rules: {
                'Label'  : {required: true },
                'DeploymentType'  : {required: true },
                'DeploymentStage' : {required: true },
                'ProjectsUsed[]' : {required: true },
                'CurrentReleases[]': {required: true },
                'ServicesDeploymentsWorkloads[]': {required: true },
                'EnterpriseDeploymentsWorkloads[]': {required: true },
                'HorizontalWorkloadFrameworks[]': {required: true },
                'CountriesPhysicalLocation': {required: true },
                'CountriesUsersLocation': {required: true }
            },
            onfocusout: false,
            focusCleanup: true,
            ignore: [],
            invalidHandler: jqueryValidatorInvalidHandler,
            errorPlacement: jqueryValidatorErrorPlacement
        });

        //custom rule for survey deployment others
        setCustomValidationRuleForOtherText($('#DeploymentSurveyDeploymentDetailsForm_Form_ServicesDeploymentsWorkloads_Other'), $('#OtherServicesDeploymentsWorkloads'));
        //custom rule for enterprise deployment others
        setCustomValidationRuleForOtherText($('#DeploymentSurveyDeploymentDetailsForm_Form_EnterpriseDeploymentsWorkloads_Other'), $('#OtherEnterpriseDeploymentsWorkloads'));
        //custom rule for other workload fwk
        setCustomValidationRuleForOtherText($('#DeploymentSurveyDeploymentDetailsForm_Form_HorizontalWorkloadFrameworks_Other'), $('#OtherHorizontalWorkloadFrameworks'));
        // combo boxes
        $("#DeploymentSurveyDeploymentDetailsForm_Form_CountriesPhysicalLocation").chosen();
        $("#DeploymentSurveyDeploymentDetailsForm_Form_CountriesUsersLocation").chosen();

        setStep('deployments');

        form.submit(function( event ) {
            var valid = form.valid();
            if(!valid){
                event.preventDefault();
                return false;
            }
            $("#DeploymentSurveyDeploymentDetailsForm_Form_CountriesPhysicalLocation").trigger('chosen:updated');
            $("#DeploymentSurveyDeploymentDetailsForm_Form_CountriesUsersLocation").trigger('chosen:updated');
        });
    });

})(jQuery);

