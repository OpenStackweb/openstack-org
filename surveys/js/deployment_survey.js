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
function checkOtherTextVisibility(chk, question_container){
    if (chk.is(':checked')) {
        question_container.removeClass('hidden');
        jQuery('.textarea',question_container).removeClass('hidden');
    }
    else {
        question_container.addClass('hidden');
        jQuery('.textarea',question_container).addClass('hidden');
    }
}


function checkOtherContainerVisibility(chk, question_container, callback){
    if (chk.is(':checked')) {
        question_container.removeClass('hidden');
        callback(chk);
    }
    else {
        question_container.addClass('hidden');
        callback(chk);
    }
}

function checkGroupOtherContainerVisibility(chk_group, question_container, callback){
    var show = false;
    $.each(chk_group, function(index , chk){
        show = show || chk.is(':checked');
        callback(chk);
    });

    if (show) {
        question_container.removeClass('hidden');
    }
    else {
        question_container.addClass('hidden');
    }
}

function setCustomValidationRuleForOtherText(chk, question_container){

    chk.click(function (e) {
        checkOtherTextVisibility(jQuery(this), question_container);
    });
    checkOtherTextVisibility(chk, question_container);

    jQuery('.textarea', question_container).rules('add', { required: function (element) {
        return chk.is(':checked');
    }});
}

function checkOtherTextVisibilityDropdown(ddl, question_container, label){
    if (ddl.val() == label) {
        question_container.removeClass('hidden');
        jQuery('.textarea',question_container).removeClass('hidden');
    }
    else {
        question_container.addClass('hidden');
        jQuery('.textarea',question_container).addClass('hidden');
    }
}

function setCustomValidationRuleForOtherTextDropdown(ddl, question_container, label){

    label = typeof label !== 'undefined' ? label : 'Other';

    ddl.change(function(e){
        checkOtherTextVisibilityDropdown(jQuery(this), question_container, label)
    });
    checkOtherTextVisibilityDropdown(ddl, question_container, label);
    jQuery('.textarea', question_container).rules('add', { required: function (element) {
        return ddl.val() == label;
    }});
}

function setCustomValidationRuleForDependantQuestion(chk_group, question_container, callback){

    $.each(chk_group, function(index , chk){
        chk.click(function (e) {
            checkGroupOtherContainerVisibility(chk_group, question_container, callback);
        });
    });

    checkGroupOtherContainerVisibility(chk_group, question_container, callback);

}

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
    if(error_container.length == 0)
        error_container = element;
    if(!$(error_container).is(':visible')){
        error_container = error_container.next();
    }
    error.appendTo(error_container);
}

function jqueryValidatorInvalidHandler(form, validator) {
    var errors = validator.numberOfInvalids();
    if (errors) {
        var first_error  = $(validator.errorList[0].element);
        if(!$(first_error).is(':visible')){
            do {
                first_error = (first_error.attr('type') == 'hidden' )?first_error.prev(): first_error.next();
            } while(!$(first_error).is(':visible'));
        }
        $('html, body').animate({
            scrollTop: first_error.offset().top
        }, 2000);
    }
}

jQuery(document).ready(function($) {

});
