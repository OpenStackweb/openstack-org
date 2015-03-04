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


    var form  = $('#DeploymentSurveyAboutYouForm_Form');
    var form_validator = null;

    form_validator = form.validate({
        rules: {
            'FirstName'  : {required: true },
            'Surname'  : {required: true },
            'DeploymentStage' : {required: true },
            'Email' : {required: true },
            'OpenStackActivity[]' : {required: true }

        },
        ignore: [],
        invalidHandler: jqueryValidatorInvalidHandler,
        errorPlacement: jqueryValidatorErrorPlacement
    });

    setCustomValidationRuleForOtherText($('#DeploymentSurveyAboutYouForm_Form_OpenStackActivity_Noneofthese'), $('#OpenStackRelationship'));
});
