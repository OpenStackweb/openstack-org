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

    var registration_form = $('.registration-form');

    var country = $(".countrydropdowncountrydropdown", registration_form);

    if(country.length>0)
        country.chosen();


    var default_country = country.val();
    var state_input     = $("input[name='State']", registration_form);

    if(registration_form.length > 0){

        $('#g_recaptcha_hidden').val('');
        $('.autocompleteoff').attr('autocomplete', 'off');

        country.chosen().change(function(){
            var country_selected = $(this).val();
            
            if(country_selected == 'US'){
                state_input.rules( "add", {required: true} );
            }else{
                state_input.rules( "remove", "required" );
            }
        });

        //custom validation
        $.validator.addMethod('checkAffiliations', function(value, element,params) {
            var count = $("#edit-affiliation-form").affiliations('count');
            return count > 0;
        },'You must add at least one Affiliation.');

        $.validator.addMethod('checkGender', function(value, element,params) {
            var gender = $("input[name='Gender']:checked").val();
            if (gender) {
                if (gender == 'Specify') {
                    return ($.trim($("input[name='GenderSpecify']").val()) != '');
                } else {
                    return true;
                }
            } else {
                return false;
            }
        },'Please specify your gender.');

        $.validator.addMethod('regex',function(value, element, regexp) {
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
                    var offset = (element.name == 'HiddenAffiliations') ? $(element).prev().offset().top : $(element).offset().top;

                    if(element.name == 'g_recaptcha_hidden'){
                        offset = $('#g-recaptcha-container').offset().top;
                    }

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
                StatementOfInterest:{required: true},
                Address:{required: true},
                City:{required: true},
                State:{required: true},
                Postcode:{required: true},
                'Password[_Password]': {required: true,minlength: 5},
                'Password[_ConfirmPassword]': {required: true,minlength: 5,equalTo: '#Password-_Password'},
                'HiddenAffiliations':{ checkAffiliations:true },
                'Gender':{checkGender:true},
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
                StatementOfInterest:'Statement of Interest is required.',
                Address:'Street Address (Line1) is required.',
                City:'City is required.',
                State:'State is required.',
                Postcode:'Postcode is required.',
                Email:{
                    required:'Primary Email Address is required.',
                    email:'Primary Email Address is not valid.',
                    remote:'That address is already in use by another user.'
                },
                'g_recaptcha_hidden': { required: 'Please confirm that you are not a robot.'}
            }
        });

        $( "#HiddenAffiliations" ).on("affiliation:saved", function( event ) {
            registration_form_validator.resetForm();
        });

        var GenderSpecify = $('input[name=GenderSpecify]');
        var GenderField = $('input[name=Gender]');
        
        GenderField.on('change',function(){
            var value = $(this).val();
            if(value == 'Specify'){
                $('#GenderSpecify').removeClass('hide').fadeIn();
                GenderSpecify.removeClass('hide').fadeIn();
            } else {
                $('#GenderSpecify').fadeOut();
                GenderSpecify.fadeOut();
            }
        });

        GenderSpecify.on('change',function(){
            $("label.error[for='Gender']").remove();
        });

        registration_form.submit(function(event){
            if(!registration_form.valid()) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
            var data_source = $("#edit-affiliation-form").affiliations('local_datasource');
            $("#HiddenAffiliations").val(JSON.stringify(data_source));
            return true;
        })
    }

    if(default_country != 'US'){
        state_input.rules( "remove", "required" );
    }

    $('.foundation-member').click(function(evt){
        $('.foundation-member').addClass('active');
        $('.community-member').removeClass('active');
        $('.termsBox').show();
        $('#terms-title').show();
        $('#member-application-title').text('2. Complete The Individual Member Application.');
        $('#foundation-disclaimer').show();
        $('#HoneyPotForm_RegistrationForm_MembershipType').val('foundation');
    });

    $('.community-member').click(function(evt){
        $('.community-member').addClass('active');
        $('.foundation-member').removeClass('active');
        $('.termsBox').hide();
        $('#terms-title').hide();
        $('#member-application-title').text('Complete The Individual Member Application.');
        $('#foundation-disclaimer').hide();
        $('#HoneyPotForm_RegistrationForm_MembershipType').val('community');
    });

    var membership = $.QueryString["membership-type"];
    switch(membership)
    {
        case 'foundation':
            $('.foundation-member').trigger('click');
            break;
        case 'community':
            $('.community-member').trigger('click');
            break;
        default:
            $('.foundation-member').trigger('click');
            break;
    }
});
