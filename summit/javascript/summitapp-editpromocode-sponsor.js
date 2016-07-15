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
    var sponsor_id = $('#sponsor_id').val();
    var summit_id = $('#summit_id').val();

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

    if (!$.isEmptyObject(sponsor)) {
        $('#company_id').tagsinput('add', sponsor);
    }

    //CODE TAG INPUT  --------------------------------------------//
    $('#code').tagsinput({
        freeInput: true,
        trimValue: true,
        tagClass: 'label-success label',
    });

    // FORM SUBMIT

    var form = $('#edit-promocode-sponsor-form');

    //validation
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            codes: { required: true},
        },
    });

    form.submit(function (evt) {
        evt.preventDefault();

        if (!form.valid()) return false;

        form.find(':submit').attr('disabled','disabled');
        var disabled = form.find(':input:disabled').removeAttr('disabled');
        var request   = form.serializeForm();
        disabled.prop('disabled',true);
        var summit_id = $('#summit_id').val();
        var method    = (sponsor_id) ? 'PUT' : 'POST';
        var url       = (sponsor_id) ? 'api/v1/summits/'+summit_id+'/registration-codes/sponsors/'+sponsor_id : 'api/v1/summits/'+summit_id+'/registration-codes/sponsors';

        $.ajax({
            type: method,
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).done(function(sponsor_id) {
            swal({
                    title: "Updated!",
                    text: "Sponsor was updated successfully.",
                    type: "success"
                },
                function() {
                    window.location.href = this_url + sponsor_id;
                });
        }).fail(function(jqXHR) {
            var responseCode = jqXHR.status;
            if(responseCode == 412) {
                var response = $.parseJSON(jqXHR.responseText);
                swal('Validation error', response.messages[0].message, 'warning');
            } else {
                swal('Error', 'There was a problem updating the sponsor, please contact admin.', 'warning');
            }
            form.find(':submit').removeAttr('disabled');
        });
        return false;
    });


});