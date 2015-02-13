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

    var form  = $('#DeploymentSurveyAppDevSurveyForm_Form');
    var form_validator = null;

    form_validator = form.validate({
        rules: {
        },
        ignore: [],
        invalidHandler: jqueryValidatorInvalidHandler,
        errorPlacement: jqueryValidatorErrorPlacement
    });

    setCustomValidationRuleForOtherText($('#DeploymentSurveyAppDevSurveyForm_Form_Toolkits_Other'), $('#OtherToolkits'));
    setCustomValidationRuleForOtherText($('#DeploymentSurveyAppDevSurveyForm_Form_ProgrammingLanguages_Other'), $('#OtherProgrammingLanguages'));
    setCustomValidationRuleForOtherText($('#DeploymentSurveyAppDevSurveyForm_Form_APIFormats_Other'), $('#OtherAPIFormats'));
    setCustomValidationRuleForOtherText($('#DeploymentSurveyAppDevSurveyForm_Form_OperatingSystems_Other'), $('#OtherOperatingSystems'));
    setCustomValidationRuleForOtherText($('#DeploymentSurveyAppDevSurveyForm_Form_GuestOperatingSystems_Other'), $('#OtherGuestOperatingSystems'));
    setCustomValidationRuleForOtherTextDropdown($('#DeploymentSurveyAppDevSurveyForm_Form_DocsPriority'), $('#OtherDocsPriority'));
    setStep('appdevsurvey');
});