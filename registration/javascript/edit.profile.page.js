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

    var form_id ="#EditProfileForm_EditProfileForm";

    var edit_profile_form = $(form_id);

    if(edit_profile_form.length > 0){

        var country = $(form_id+'_Country');
        if(country.length > 0){
            country.chosen();
        }

        var default_country = country.val();
        state_input = $(form_id + ' input[name="State"]');

        $('.autocompleteoff').attr('autocomplete', 'off');

        var country = $(form_id+'_Country');

        //custom validation
        jQuery.validator.addMethod('checkAffiliations', function(value, element,params) {
            var count = $("#edit-affiliation-form").affiliations('count');
            return count  > 0;
        },'You must add at least one Affiliation.');

        jQuery.validator.addMethod('checkGender', function(value, element,params) {
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

        jQuery.validator.addMethod(
            "regex",
            function(value, element, regexp) {
                var re = new RegExp(regexp,'g');
                var res =  re.test(value);
                return !res;
            },
            "Please check your input."
        );

        edit_profile_form.validate({
            onfocusout: false,
            invalidHandler: function(form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    var element = validator.errorList[0].element;
                    var offset = (element.name == 'Affiliations') ? $(element).prev().offset().top : $(element).offset().top;
                    $('html, body').animate({
                        scrollTop: offset-100
                    }, 2000);
                }
            },
            ignore: [],
            rules: {
                FirstName:{required: true,regex:'[\"()=<>]+' },
                Surname:{required: true,regex:'[\"()=<>]+'},
                Email: {required: true,email: true},
                SecondEmail:{required: false,email: true},
                StatementOfInterest:{required: true},
                ThirdEmail:{required: false,email: true},
                OrgName:{required: true},
                Address:{required: true},
                City:{required: true},
                State:{required: true},
                Postcode:{required: true},
                'HiddenAffiliations':{ checkAffiliations:true },
                'Gender':{ checkGender:true }
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
                OrgName:'Affiliations is required.',
                StatementOfInterest:'Statement of Interest is required.',
                Address:'Street Address (Line1) is required.',
                City:'City is required.',
                State:'State is required.',
                Postcode:'Postcode is required.',
                Email:{
                    required:'Primary Email Address is required.',
                    email:'Primary Email Address is not valid.'
                },
                SecondEmail:'Second Email Address is not valid.',
                ThirdEmail:'Third Email Address is not valid.'
            }
        });

        country.change( function(){

            var state_input = $(form_id + ' input[name="State"]');

            var country_selected = $(this).val();

            if(country_selected == 'US'){
                state_input.rules( "add", {required: true} );
            }else{
                state_input.rules( "remove", "required" );
            }

        });


        var GenderSpecify = $('input[name=GenderSpecify]');
        var GenderField = $('input[name=Gender]');

        if(GenderSpecify.val() != ''){
            $('#GenderSpecify').show();
            GenderSpecify.show();
            $('#EditProfileForm_EditProfileForm_Gender_Specify').prop("checked", true);
        }

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

        var state_input = $(form_id + ' input[name="State"]');

        if(default_country != 'US'){
            state_input.rules( "remove", "required" );
        }

        $(form_id+'_FirstName').change(function(){
            if (confirm("We see you just updated your Profile Name, would you like to copy this to your Speaker Name?") == true) {
                $(form_id+'_ReplaceName').val(1);
            } else {
                $(form_id+'_ReplaceName').val(0);
            }
        });

        $(form_id+'_Surname').change(function(){
            if (confirm("We see you just updated your Profile Last Name, would you like to copy this to your Speaker Last Name?") == true) {
                $(form_id+'_ReplaceSurname').val(1);
            } else {
                $(form_id+'_ReplaceSurname').val(0);
            }
        });

        // see if Bio has been modified, we do it this way because we can't make the textarea onchange event work
        edit_profile_form.submit(function()
        {
            if (tinyMCE.activeEditor.isDirty()) {
                if (confirm("We see you just updated your Profile Bio, would you like to copy this to your Speaker Bio?") == true) {
                    $(form_id+'_ReplaceBio').val(1);
                    return true;
                } else {
                    $(form_id+'_ReplaceBio').val(0);
                    return true;
                }
            }
        });


    }



    var speaker_form_id ="#EditSpeakerProfileForm_EditSpeakerProfileForm";

    var edit_speaker_profile_form = $(speaker_form_id);

    if(edit_speaker_profile_form.length > 0){

        //custom validation
        $.validator.addMethod(
            "regex",
            function(value, element, regexp) {
                var re = new RegExp(regexp,'g');
                var res =  re.test(value);
                return !res;
            },
            "Please check your input."
        );

        edit_speaker_profile_form.validate({
            onfocusout: false,
            invalidHandler: function(form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    $('html, body').animate({
                        scrollTop: $(validator.errorList[0].element).offset().top-100
                    }, 2000);
                }
            },
            ignore: [],
            rules: {
                FirstName:{required: true, ValidPlainText:true },
                Surname:{required: true, ValidPlainText:true },
                Title: {required: true, ValidPlainText:true},
                Bio:{required: true},
                'Expertise[1]':{required: true, ValidPlainText:true },
                'Expertise[2]':{ValidPlainText:true },
                'Expertise[3]':{ValidPlainText:true },
                'Expertise[4]':{ValidPlainText:true },
                'Expertise[5]':{ValidPlainText:true },
                'PresentationLink[1]' : {required: true, url: true},
                'PresentationLink[2]' : {url: true},
                'PresentationLink[3]' : {url: true},
                'PresentationLink[4]' : {url: true},
                'PresentationLink[5]' : {url: true},
                'PresentationTitle[1]' : {ValidPlainText: true},
                'PresentationTitle[2]' : {ValidPlainText: true},
                'PresentationTitle[3]' : {ValidPlainText: true},
                'PresentationTitle[4]' : {ValidPlainText: true},
                'PresentationTitle[5]' : {ValidPlainText: true},
                'Language[1]' : {required: true,ValidPlainText:true},
                'Language[2]':{ValidPlainText:true },
                'Language[3]':{ValidPlainText:true },
                'Language[4]':{ValidPlainText:true },
                'Language[5]':{ValidPlainText:true },
                WillingToTravel : {required: true},
                'IRCHandle':{ ValidPlainText:true },
                'TwitterName':{ ValidPlainText:true }
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
                Title: {
                    required: 'Title is required.',
                    regex: 'Title is not valid.'
                },
                Bio:'Bio is required.',
                'Expertise[1]': {
                    required: 'Add at least one area of expertise.',
                    regex: 'Expertise is not valid.'
                },
                'PresentationLink[1]': {
                    required: 'Add at least one presentation link.',
                    regex: 'Link is not valid.'
                },
                'PresentationTitle[1]': {
                    required: 'Add a title to the presentation.',
                    regex: 'Title is not valid.'
                },
                'Language[1]': {
                    required: 'Add at least one language you are fluent in.',
                    regex: 'Language is not valid.'
                }
            }
        });

        $(speaker_form_id+'_FirstName').change(function(){
            if (confirm("We see you just updated your Speaker Name, would you like to copy this to your Profile Name?") == true) {
                $(speaker_form_id+'_ReplaceName').val(1);
            } else {
                $(speaker_form_id+'_ReplaceName').val(0);
            }
        });

        $(speaker_form_id+'_LastName').change(function(){
            if (confirm("We see you just updated your Speaker Last Name, would you like to copy this to your Profile Last Name?") == true) {
                $(speaker_form_id+'_ReplaceSurname').val(1);
            } else {
                $(speaker_form_id+'_ReplaceSurname').val(0);
            }
        });

        // see if Bio has been modified, we do it this way because we can't make the textarea onchange event work
        edit_speaker_profile_form.submit(function(event)
        {
            if(!edit_speaker_profile_form.valid()) return false;

            if (tinyMCE.activeEditor.isDirty()) {
                if (confirm("We see you just updated your Speaker Bio, would you like to copy this to your Profile Bio?") == true) {
                    $(speaker_form_id+'_ReplaceBio').val(1);
                    return true;
                } else {
                    $(speaker_form_id+'_ReplaceBio').val(0);
                    return true;
                }
            }
        });

    }
});