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

    var form = $('#add-speaker-form');

    //validation
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            title:      { required: true},
            first_name: { required : true },
            last_name:  { required: true },
            bio:        { required: true },
            email: { required: function(){
                return $('#email').val() === '' && $('#member_id').val() === '';
            }, email:true },
            member_id: { required: function(){
                return $('#email').val() === '' && $('#member_id').val() === '';
            }}
        },
    });

    $('#addSpeakerModal').on('hidden.bs.modal', function (e) {
        form.cleanForm();
        form_validator.resetForm();
    })

    var members_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/members?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#save-speaker').click(function(evt){
        evt.preventDefault();
        if (!form.valid()) return false;

        form.find(':button').attr('disabled','disabled');
        var request    = form.serializeForm();
        var url        = 'api/v1/summits/'+summit_id+'/speakers';
        $.ajax({
            type: 'POST',
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).done(function(saved_event) {
            swal("Updated!", "Speaker was added successfully.", "success");
            form.find(':button').removeAttr('disabled');
            $('#addSpeakerModal').modal('hide');
        }).fail(function(jqXHR) {
            var responseCode = jqXHR.status;
            if(responseCode == 412) {
                var response = $.parseJSON(jqXHR.responseText);
                swal('Validation error', response.messages[0].message, 'warning');
            } else {
                swal('Error', 'There was a problem adding speaker, please contact admin.', 'warning');
            }
            form.find(':button').removeAttr('disabled');
        });
        return false;
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

});