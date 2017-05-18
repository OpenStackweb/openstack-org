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

    var form = $("#EventRegistrationRequestForm_EventRegistrationRequestForm");
    var live_form = $("#EventForm_EventForm");
    var form_validator = null;
    var live_form_validator = null;

    if(form.length > 0){
        $.validator.addMethod("complete_url", function(val, elem) {
            if (val.length == 0) { return false; }
            // if user has not entered http:// https:// or ftp:// assume they mean http://
            if(!/^(https?|ftp):\/\//i.test(val)) {
                val = 'http://'+val; // set both the value
                $(elem).val(val); // also update the form element
            }
            // now check if valid url
            return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(val);
        });

        //main form validation
        form_validator = form.validate({
            onfocusout: false,
            focusCleanup: true,
            rules: {
                point_of_contact_name    : { required: true , ValidPlainText:true, maxlength: 100 },
                point_of_contact_email   : { required: true , email:true, maxlength: 100 },
                title      : { required: true , ValidPlainText:true, maxlength: 35 },
                url        : {required: true, complete_url: true, maxlength: 255},
                category   : {required: true},
                city       : {required: true, ValidPlainText: true, maxlength: 255},
                country    : {required: true},
                start_date  : {required: true, dpDate: true},
                end_date    : {required: true, dpDate: true, dpCompareDate:'ge #EventRegistrationRequestForm_EventRegistrationRequestForm_start_date'}
            },
            focusInvalid: false,
            invalidHandler: function(form, validator) {
                if (!validator.numberOfInvalids())
                    return;
                var element = $(validator.errorList[0].element);
                if(!element.is(":visible")){
                    element = element.parent();
                }

                $('html, body').animate({
                    scrollTop: element.offset().top - 100
                }, 1000);
            },
            errorPlacement: function(error, element) {
                if(!element.is(":visible")){
                    element = element.parent();
                }
                error.insertAfter(element);
            }
        });

        // initialize widgets

        $('#EventRegistrationRequestForm_EventRegistrationRequestForm_category',form).chosen({
            disable_search_threshold: 10,
            width: '315px'
        });
        $('#EventRegistrationRequestForm_EventRegistrationRequestForm_category',form).change(function () {
            form_validator.resetForm();
        });

        var date_picker_start = $('#EventRegistrationRequestForm_EventRegistrationRequestForm_start_date',form);
        date_picker_start.datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0
        });

        var date_picker_end = $('#EventRegistrationRequestForm_EventRegistrationRequestForm_end_date',form);
        date_picker_end.datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0
        });

        $('#EventRegistrationRequestForm_EventRegistrationRequestForm_country',form).chosen({
            disable_search_threshold: 10,
            width: '315px'
        });
        $('#EventRegistrationRequestForm_EventRegistrationRequestForm_country',form).change(function () {
            form_validator.resetForm();
        });
    }


    if(live_form.length > 0){

        live_form_validator = live_form.validate({
            onfocusout: false,
            focusCleanup: true,
            rules: {
                title           : { required: true , ValidPlainText:true, maxlength: 35 },
                url             : {required: true, url: true, maxlength: 255},
                event_category  : {required: true},
                location        : {required: true, ValidPlainText: true, maxlength: 255},
                start_date      : {required: true, dpDate: true},
                end_date        : {required: true, dpDate: true, dpCompareDate:'ge #EventForm_EventForm_start_date'}
            },
            focusInvalid: false,
            invalidHandler: function(form, validator) {
                if (!validator.numberOfInvalids())
                    return;
                var element = $(validator.errorList[0].element);
                if(!element.is(":visible")){
                    element = element.parent();
                }
            },
            errorPlacement: function(error, element) {
                if(!element.is(":visible")){
                    element = element.parent();
                }
                error.insertAfter(element);
            }
        });

    }


});