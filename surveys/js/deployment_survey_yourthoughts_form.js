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

    setStep('yourthoughts');

    var form  = $('#DeploymentSurveyYourThoughtsForm_Form');
    var form_validator = null;

    $.validator.addMethod("sortable_drivers", function (value, element) {
        $.validator.messages.sortable_drivers = 'You must select at least one business driver.';
        var sorted = $("#options ol").sortable( "toArray");

        var count  = sorted.length;
        for(var i = 0; i < count; i++){
            if(sorted[i] == '6311ae17c1ee52b36e68aaf4ad066387_answer' && $('#other_txt',  $("#options ol")).val()==='' ){
                $.validator.messages.sortable_drivers = 'You must specify you custom business driver.';
                return false;
            }
        }
        return count > 0;
    },'');

    form_validator = form.validate({
        rules: {
            'BusinessDrivers'  : {sortable_drivers: true },
            'InformationSources[]'  : {required: true }
        },
        ignore: [],
        invalidHandler: jqueryValidatorInvalidHandler,
        errorPlacement: function (error, element) {
            if(element.hasClass('hidden'))
            {
                error.appendTo($('#options'));
            }
            else
                jqueryValidatorErrorPlacement(error, element);
        }

    });

    setCustomValidationRuleForOtherText($('#DeploymentSurveyYourThoughtsForm_Form_InformationSources_Other'), $('#OtherInformationSources'));

    form.submit(function( event ) {
        var valid = form.valid();
        var sorted = $("#options ol").sortable("toArray");
        var count  = sorted.length;
        var drivers = '';
        for(var i = 0; i < count; i++){
            var key   = sorted[i];
            var value = answer_table[key];
            if(key == '6311ae17c1ee52b36e68aaf4ad066387_answer'){
                $('#DeploymentSurveyYourThoughtsForm_Form_OtherBusinessDrivers').val($('#other_txt',  $("#options ol")).val());
            }
            drivers += value +',';
        }
        //remove last ,
        drivers = drivers.substring(0, drivers.length - 1);
        $('#DeploymentSurveyYourThoughtsForm_Form_BusinessDrivers').val(drivers);

        if(!valid){
            event.preventDefault();
            return false;
        }

    });

});