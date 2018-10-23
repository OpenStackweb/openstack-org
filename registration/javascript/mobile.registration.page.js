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


    var registration_form = $('.mobile-registration-form');

    if(registration_form.length > 0){

        //custom validation
        $('#g_recaptcha_hidden').val('');

        jQuery.validator.addMethod('regex',function(value, element, regexp) {
                var re = new RegExp(regexp,'g');
                var res =  re.test(value);
                return !res;
            },'Please check your input.');

        var registration_form_validator = registration_form.validate({
            onfocusout: false,
            invalidHandler: function(form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    var element = validator.errorList[0].element;
                    var offset = $(element).offset().top;
                    $('html, body').animate({
                        scrollTop: offset-100
                    }, 2000);
                }
            },
            errorPlacement: function(error, element) {
                if($(element).attr('name') == 'g_recaptcha_hidden'){
                    error.insertAfter($('#g-recaptcha-container'));
                }
                if($(element).attr('name') == 'HiddenAffiliations'){
                    error.insertAfter($(".middleColumn", $('#Affiliations')));
                }
                if($(element).attr('name') == 'Gender'){
                    error.insertAfter($("#Gender"));
                }
                else {
                    error.insertAfter($(element).closest('div.middleColumn'));
                }
            },
            ignore: [],
            rules: {
                FirstName:{required: true,regex:'[\"()=<>]+'},
                Surname:{required: true,regex:'[\"()=<>]+'},
                Email: {required: true,email: true, remote: '/RegistrationPage_Controller/CheckEmail'},
                'Password[_Password]': {required: true,minlength: 5},
                'Password[_ConfirmPassword]': {required: true,minlength: 5,equalTo: '#Password_Password'},
                'g_recaptcha_hidden': {required: true}
            },
            messages: {
                FirstName:{
                    required:'First Name is required.',
                    regex:'First Name is not valid.'
                },
                Surname:{
                    required:'Last Name is required.',
                    regex:'Last Name is not valid.'
                },
                Email:{
                    required:'Primary Email Address is required.',
                    email:'Primary Email Address is not valid.',
                    remote:'That address is already in use by another user.'
                },
                'g_recaptcha_hidden': { required: 'Please confirm that you are not a robot.'}
            }
        });

        registration_form.submit(function(ev){
            if (!registration_form_validator.valid()) return false;
        });
    }
});
