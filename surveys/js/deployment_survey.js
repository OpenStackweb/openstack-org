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
function checkOtherTextVisibility(chk, container){
    if (chk.is(':checked')) {
        container.removeClass('hidden');
        jQuery('.textarea',container).removeClass('hidden');
    }
    else {
        container.addClass('hidden');
        jQuery('.textarea',container).addClass('hidden');
    }
}

function setCustomValidationRuleForOtherText(chk, text_container){

    chk.click(function (e) {
        checkOtherTextVisibility($(this), text_container);
    });
    checkOtherTextVisibility(chk, text_container);

    jQuery('.textarea', text_container).rules('add', { required: function (element) {
        return chk.is(':checked');
    }});
}

function jqueryValidatorErrorPlacement(error, element) {
    if(element.hasClass('checkbox'))
        error.appendTo( element.parents('div[class*="checkboxset"]'));
    if(element.hasClass('text'))
        error.appendTo(element.parents('div[class*="text"]'));
    if(element.hasClass('dropdown'))
        error.appendTo(element.parents('div[class*="dropdown"]'));
    if(element.hasClass('textarea'))
        error.appendTo(element.parents('div[class*="textarea"]'));
    if(element.hasClass('multidropdown'))
        error.appendTo(element.parents('div[class*="multidropdown"]'));
}

function jqueryValidatorInvalidHandler(form, validator) {
    var errors = validator.numberOfInvalids();
    if (errors) {
        validator.errorList[0].element.focus();
    }
}
