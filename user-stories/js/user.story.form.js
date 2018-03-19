/**
 * Copyright 2017 OpenStack Foundation
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
var form_id = '#UserStoryForm_UserStoryForm';
var summit_id = 22; // dummy value, we only need this to access the api

$(document).ready(function(){

    $(form_id+"_Locations").chosen({width: '100%'});

    form_validator = $(form_id).validate(
    {
        rules: {
            Link: {
                required: true,
                url: true
            }
        },
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

    var companies = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/companies?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $(form_id+'_Organization').tagsinput({
        itemValue: 'id',
        itemText: 'name',
        freeInput: false,
        maxTags: 1,
        trimValue: true,
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 3
            },
            {
                name: 'companies',
                displayKey: 'name',
                source: companies,
                limit: 20
            }
        ]
    });


    var tags_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/tags?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $(form_id+'_Tags').tagsinput({
        itemValue: 'id',
        itemText: 'name',
        freeInput: false,
        allowDuplicates: false,
        trimValue: true,
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 3
            },
            {
                name: 'tags_source',
                displayKey: 'name',
                source: tags_source,
                limit: 20
            }
        ]
    });

    if (typeof org !== 'undefined' && !$.isEmptyObject(org)) {
        $(form_id + '_Organization').tagsinput('add', org);
    }

    if (typeof tags !== 'undefined') {
        $.each(tags, function(index, value) {
            $(form_id + '_Tags').tagsinput('add', value);
        });
    }

    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('.btn-delete').attr('href', $(e.relatedTarget).data('href'));
    });

});