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



    $.validator.addMethod("ValidNewOrgName", function (value, element, arg) {
        value = value.trim();
        var field = arg[0];
        if(value=='0' && field.val().trim()==='')
            return false;
        return true;
    }, "You Must Specify a New Organization Name!.");


    $('#DeploymentSurveyYourOrganizationForm_Form_PrimaryCountry').chosen();

    setStep('yourorganization');

    var form  = $('#DeploymentSurveyYourOrganizationForm_Form');
    var form_validator = null;

    form_validator = form.validate({
        rules: {
            'Organization'  : {required: true },
            'Industry'  : {required: true },
            'PrimaryCountry' : {required: true },
            'PrimaryState' : {required: true },
            'PrimaryCity': {required: true },
            'OpenStackInvolvement[]': {required: true }
        },
        ignore: [],
        invalidHandler: jqueryValidatorInvalidHandler,
        errorPlacement: jqueryValidatorErrorPlacement
    });

    setCustomValidationRuleForOtherTextDropdown($('#DeploymentSurveyYourOrganizationForm_Form_Industry'), $('#OtherIndustry'));

    setCustomValidationRuleForOtherTextDropdown($('#DeploymentSurveyYourOrganizationForm_Form_Industry'), $('#ITActivity'), 'Information Technology');


    var ddl   = $('#DeploymentSurveyYourOrganizationForm_Form_OrgID',form);
    var input = $('#DeploymentSurveyYourOrganizationForm_Form_Organization',form);

    input.autocomplete('/join/register/results', {
        minChars: 3,
        selectFirst: true,
        autoFill: true
    });

    if(ddl.length > 0) {
        var new_org_name = $('.new-org-name',form);
        new_org_name.hide();
        ddl.rules('add',{
            required:true,
            ValidNewOrgName:[new_org_name]
        });
        ddl.change(function(event){
            var ddl = $(this);
            if(ddl.val()=='0'){
                new_org_name.show();
                form_validator.resetForm();
            }
            else{
                new_org_name.hide();
            }
        });
    }
    else if(input.length > 0) {
        input.rules('add',{required:true});
    }
});
