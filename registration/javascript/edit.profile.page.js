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

    if(edit_profile_form.length > 0) {

        //custom validation
        $.validator.addMethod('checkAffiliations', function (value, element, params) {
            var count = $("#edit-affiliation-form").affiliations('count');
            return count > 0;
        }, 'You must add at least one Affiliation.');

        $.validator.addMethod('checkGender', function (value, element, params) {
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
        }, 'Please specify your gender.');

        $.validator.addMethod(
            "regex",
            function (value, element, regexp) {
                var re = new RegExp(regexp, 'g');
                var res = re.test(value);
                return !res;
            },
            "Please check your input."
        );

        edit_profile_form.validate({
            onfocusout: false,
            invalidHandler: function (form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    var element = validator.errorList[0].element;
                    var offset = (element.name == 'Affiliations') ? $(element).prev().offset().top : $(element).offset().top;
                    $('html, body').animate({
                        scrollTop: offset - 100
                    }, 2000);
                }
            },
            ignore: [],
            rules: {
                OrgName: {required: true},
                'HiddenAffiliations': {checkAffiliations: true},
                'Gender': {checkGender: true}
            },
            messages: {
                OrgName: 'Affiliations is required.',
            }
        });

        var GenderSpecify = $('input[name=GenderSpecify]');
        var GenderField = $('input[name=Gender]');

        if (GenderSpecify.val() != '') {
            $('#GenderSpecify').show();
            GenderSpecify.show();
            $('#EditProfileForm_EditProfileForm_Gender_Specify').prop("checked", true);
        }

        GenderField.on('change', function () {
            var value = $(this).val();
            if (value == 'Specify') {
                $('#GenderSpecify').removeClass('hide').fadeIn();
                GenderSpecify.removeClass('hide').fadeIn();
            } else {
                $('#GenderSpecify').fadeOut();
                GenderSpecify.fadeOut();
            }
        });

        GenderSpecify.on('change', function () {
            $("label.error[for='Gender']").remove();
        });
    }


    var speaker_form_id ="#EditSpeakerProfileForm_EditSpeakerProfileForm";

    var edit_speaker_profile_form = $(speaker_form_id);

    if(edit_speaker_profile_form.length > 0){

        var country = $(speaker_form_id+'_CountriesToTravel');
        if(country.length > 0){
            country.chosen({width: '100%'});
        }

        var willing_travel = $(speaker_form_id+"_WillingToTravel");
        country.prop('disabled', willing_travel.prop('checked')).trigger("chosen:updated");

        willing_travel.change(function() {
            let diabled = $(this).prop('checked');
            country.prop('disabled', diabled).trigger("chosen:updated");
        });

        var languages = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: $.map(language_source, function (language) {
                return {
                    name: language.name,
                    id: language.id
                };
            })
        });

        languages.initialize();

        $(speaker_form_id+'_Languages').tagsinput({
            freeInput: false,
            maxTags: 5,
            trimValue: true,
            typeaheadjs: [
                {
                    hint: true,
                    highlight: true,
                    minLength: 3
                },
                {
                    minlength: 3,
                    name: 'languages',
                    displayKey: 'name',
                    valueKey: 'name',
                    source: languages.ttAdapter()
                }
            ]
        });

        // Areas of Expertise

        $(speaker_form_id+'_Expertise').tagsinput({
            freeInput: true,
            maxTags: 5,
            trimValue: true
        });

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
                'Expertise':{required: true, ValidPlainText:true },
                'PresentationLink[1]' : {url: true},
                'PresentationLink[2]' : {url: true},
                'PresentationLink[3]' : {url: true},
                'PresentationLink[4]' : {url: true},
                'PresentationLink[5]' : {url: true},
                'PresentationTitle[1]' : {ValidPlainText: true},
                'PresentationTitle[2]' : {ValidPlainText: true},
                'PresentationTitle[3]' : {ValidPlainText: true},
                'PresentationTitle[4]' : {ValidPlainText: true},
                'PresentationTitle[5]' : {ValidPlainText: true},
                'Languages' : {required: true,ValidPlainText:true},
                'GitHubUser':{ ValidPlainText:true },
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


    }
});