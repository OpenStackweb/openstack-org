/**
 * Copyright 2015 OpenStack Foundation
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

var form_validator = null;

$(document).ready(function(){

    form_validator = $('#SpeakerForm_BioForm').validate(
        {
            ignore:[],
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function(error, element) {
                if(element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            invalidHandler: function(form, validator) {
                if (!validator.numberOfInvalids())
                    return;
                var element = $(validator.errorList[0].element);
                if(!element.is(":visible")){
                    element = element.parent();
                }

                $('html, body').animate({
                    scrollTop: element.offset().top
                }, 2000);
            },
        });

    $("#SpeakerForm_BioForm_CountriesToTravel").chosen({width: '100%'});

    var language_source = ['Afrikaans','Apprenda','Arabic','Armenian','ASL Sign Language','Bahasa','Basque','Belarusian',
        'Bengali','Brazilian Portuguese','Bulgarian','Burmese','Canadian','Cantonese','Catalan','Chinese','Croatian','Czech',
        'Danish','Deutsch','Dutch','English','Esperanto','Estonian','Farsi','Filipino','Finnish','Flemish','French','Georgian',
        'German','Gibberish','Greek','Gujrathi','Haitian Creole','Hebrew','Hindi','Indian','Italian','Japanese','Kannada',
        'Kapampangan','Kiswahili','Konkani','Korean','Lithuanian','Mandarin','Magyar','Malagasy','Malayalam','Marathi',
        'Nigerian','Norwegian','Odiya','Panjabi','Polish','Portuguese','Punjabi','Ripuarian','Romanian','Russian','Sanskrit',
        'Serbian','Slovenian','Spanish','Sundanese','Svenska','Swedish','Taiwanese','Tamazight','Tamil','Telugu','Turkish',
        'Ukrainian','Urdu','Uyghur','Uzbek','Vietnamese'
    ];

    var languages = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: $.map(language_source, function (language) {
            return {
                name: language
            };
        })
    });
    languages.initialize();

    $('#SpeakerForm_BioForm_Language').tagsinput({
        freeInput: true,
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

    $('#SpeakerForm_BioForm_Expertise').tagsinput({
        freeInput: true,
        maxTags: 5,
        trimValue: true
    });

});