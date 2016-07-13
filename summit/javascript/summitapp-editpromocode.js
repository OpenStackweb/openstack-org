/**
 * Copyright 2016 OpenStack Foundation
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
    var promocode_id = $('#promocode_id').val();
    var summit_id = $('#summit_id').val();

    updateForm();

    $('#code_type').change(function(){
       updateForm();
    });

    if (promocode_id) {
        $('#code_type').prop('disabled',true);
        $('#code').prop('disabled',true);
    }

    // MEMBER AUTOCOMPLETE  --------------------------------------------//
    var members_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/members?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#member_id').tagsinput({
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
                name: 'members_source',
                displayKey: 'name',
                source: members_source
            }
        ]
    });

    if (!$.isEmptyObject(owner)) {
        $('#member_id').tagsinput('add', owner);
    }

    //SPEAKER AUTOCOMPLETE  --------------------------------------------//
    var speakers_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/speakers/%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#speaker_id').tagsinput({
        itemValue: 'speaker_id',
        itemText: 'name',
        freeInput: false,
        allowDuplicates: false,
        trimValue: true,
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'speakers_source',
                displayKey: 'name',
                source: speakers_source
            }
        ]
    });

    if (!$.isEmptyObject(speaker)) {
        $('#speaker_id').tagsinput('add', speaker);
    }

    //COMPANY AUTOCOMPLETE  --------------------------------------------//
    var company_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/companies?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#company_id').tagsinput({
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
                name: 'company_source',
                displayKey: 'name',
                source: company_source
            }
        ]
    });

    if (!$.isEmptyObject(company)) {
        $('#company_id').tagsinput('add', company);
    }

    //CODE TAG INPUT  --------------------------------------------//
    $('#code').tagsinput({
        maxTags: 1
    });

    if (!$.isEmptyObject(code)) {
        $('#code').tagsinput('add', code);
    }

    // FORM SUBMIT

    var form = $('#edit-promocode-form');

    //validation
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            code:       { required: true},
        },
    });

    form.submit(function (evt) {
        evt.preventDefault();

        if (!form.valid()) return false;

        form.find(':submit').attr('disabled','disabled');
        var disabled = form.find(':input:disabled').removeAttr('disabled');
        var request      = form.serializeForm();
        disabled.prop('disabled',true);
        var summit_id    = $('#summit_id').val();
        var method       = (promocode_id) ? 'PUT' : 'POST';
        var url          = (promocode_id) ? 'api/v1/summits/'+summit_id+'/registration-codes/'+promocode_id : 'api/v1/summits/'+summit_id+'/registration-codes/';

        $.ajax({
            type: method,
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).done(function(promocode) {
            swal({
                    title: "Updated!",
                    text: "Promo Code was updated successfully.",
                    type: "success"
                },
                function() {
                    window.location.href = this_url + promocode;
                });
        }).fail(function(jqXHR) {
            var responseCode = jqXHR.status;
            if(responseCode == 412) {
                var response = $.parseJSON(jqXHR.responseText);
                swal('Validation error', response.messages[0].message, 'warning');
            } else {
                swal('Error', 'There was a problem updating the promo code, please contact admin.', 'warning');
            }
            form.find(':submit').removeAttr('disabled');
        });
        return false;
    });

    function updateForm() {
        var type = $('#code_type').val();
        switch (type) {
            case 'ALTERNATE' :
            case 'ACCEPTED' :
                $('.member_container').hide();
                $('.company_container').hide();
                $('.addressee_container').hide();
                $('.speaker_container').show();
                break;
            case 'VIP':
            case 'ATC':
            case 'MEDIA ANALYST':
                $('.member_container').show();
                $('.company_container').hide();
                $('.addressee_container').show();
                $('.speaker_container').hide();
                break;
            case 'SPONSOR':
                $('.member_container').show();
                $('.company_container').show();
                $('.addressee_container').show();
                $('.speaker_container').hide();
                break;
        }
    }

});