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

function jqueryValidatorErrorPlacement(error, element) {
    var error_container = null;
    if(element.hasClass('checkbox'))
        error_container = element.parents('div[class*="checkboxset"]');
    if(element.hasClass('text'))
        error_container = element.parents('div[class*="text"]');
    if(element.hasClass('dropdown'))
        error_container = element.parents('div[class*="dropdown"]');
    if(element.hasClass('textarea'))
        error_container = element.parents('div[class*="textarea"]');
    if(element.hasClass('multidropdown'))
        error_container = element.parents('div[class*="multidropdown"]');
    if(!element.is(':visible') || error_container.length == 0 || !error_container.is(':visible')){
        error_container = element.closest(":visible");
        error_container.after(error);
    }
    else
        error.appendTo(error_container);
    error.show();
}

function jqueryValidatorInvalidHandler(form, validator) {
    var errors = validator.numberOfInvalids();
    if (errors) {
        var first_error  = $(validator.errorList[0].element);
        if(!first_error.is(':visible')){
            first_error = first_error.closest(":visible" );
        }
        $('html, body').animate({
            scrollTop: first_error.offset().top
        }, 2000);
    }
}

jQuery(document).ready(function($) {

    $.validator.setDefaults({
        ignore: ".hidden:not(.chosen-visible)",
        invalidHandler: jqueryValidatorInvalidHandler,
        errorPlacement: jqueryValidatorErrorPlacement
    });

    //custom validation rules

    $.validator.addMethod("ranking_required", function (value, element, container_id) {
        var sorted  = $('.selected-rank','#'+container_id);
        return sorted.length > 0;
    });

    $('.survey_step_form').submit(function (evt){
        if($(this).valid())
            $('.action').prop('disabled', true);
    });
});