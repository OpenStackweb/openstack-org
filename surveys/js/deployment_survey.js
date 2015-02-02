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

function setCustomValidationRuleForOtherText(chk, question_container){

    chk.click(function (e) {
        checkOtherTextVisibility(jQuery(this), question_container);
    });
    checkOtherTextVisibility(chk, question_container);

    jQuery('.textarea', question_container).rules('add', { required: function (element) {
        return chk.is(':checked');
    }});
}

function checkOtherTextVisibilityDropdown(ddl, question_container){
    if (ddl.val() == 'Other') {
        question_container.removeClass('hidden');
        jQuery('.textarea',question_container).removeClass('hidden');
    }
    else {
        question_container.addClass('hidden');
        jQuery('.textarea',question_container).addClass('hidden');
    }
}

function setCustomValidationRuleForOtherTextDropdown(ddl, question_container){
    ddl.change(function(e){
        checkOtherTextVisibilityDropdown(jQuery(this), question_container)
    });
    checkOtherTextVisibility(ddl, question_container);
    jQuery('.textarea', question_container).rules('add', { required: function (element) {
        return ddl.val() == 'Other';
    }});
}

function setCustomValidationRuleForDependantQuestion(chk_group, question_container){

    $.each(chk_group, function(index , chk){

        chk.click(function (e) {
            checkOtherTextVisibility(jQuery(this), question_container);
        });

        checkOtherTextVisibility(chk, question_container);

        jQuery('.textarea', question_container).rules('add', { required: function (element) {
            return chk.is(':checked');
        }});
    });

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
