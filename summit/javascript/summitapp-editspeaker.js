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

    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        $('#image-filename').val(label);
    });

    tinymce.init({
        selector: "textarea",
        width:      '100%',
        height:     270,
        plugins:    [ "anchor link" ],
        statusbar:  false,
        menubar:    false
    });
    var summit_id = $('#summit_id').val();

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

    // promo codes

    var reg_code_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/registration-codes/%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#reg_code').tagsinput({
        itemValue: 'code',
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
                name: 'reg_code_source',
                displayKey: 'name',
                source: reg_code_source
            }
        ]
    });

    $('#member_id').on('itemAdded', function(event) {
        var regExp = /\(([^)]+)\)/;
        var matches = regExp.exec(event.item.name);
        $('#email').val(matches[1]);
    });

    if (!$.isEmptyObject(member)) {
        $('#member_id').tagsinput('add', member);
    }

    if (!$.isEmptyObject(registration_code)) {
        $('#reg_code').tagsinput('add', registration_code);
    }

    var form = $('#edit-speaker-form');

    //validation
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            email:      { email: true},
            title:      { required: true},
            first_name: { required : true },
            last_name:  { required: true },
            bio:        { required: true },
        },
    });

    form.submit(function (evt) {
        evt.preventDefault();
        var summit_id  = $('#summit_id').val();
        var speaker_id = $('#speaker_id').val();
        var url        = 'api/v1/summits/'+summit_id+'/speakers/'+speaker_id+'/pic';
        var file_data  = $("#profile-pic").prop("files")[0];
        var form_data  = new FormData();
        form_data.append("file", file_data);

        if ($('#image-filename').val()) {
            $.ajax({
                url: url,
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'POST',
                success: function(image_id){
                    updateSpeaker(image_id);
                },
                error: function(response,status,error) {
                    swal('Validation error', response.responseJSON.messages[0].message, 'warning');
                }
            });
        } else {
            updateSpeaker(0);
        }


        return false;
    });

    function updateSpeaker(profile_pic_id) {
        form.find('textarea').each(function() {
            var text_area = $(this);
            var text_editor = tinyMCE.get(text_area.attr('id'));
            if (text_editor)
                text_area.val(text_editor.getContent());
        });
        if (!form.valid()) return false;

        $('#photoID',form).val(profile_pic_id);

        form.find(':submit').attr('disabled','disabled');
        var request    = form.serializeForm();
        var summit_id  = $('#summit_id').val();
        var speaker_id = $('#speaker_id').val();
        var url        = 'api/v1/summits/'+summit_id+'/speakers/'+speaker_id;

        $.ajax({
            type: 'PUT',
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).done(function(saved_event) {
            swal("Updated!", "Speaker was updated successfully.", "success");
            form.find(':submit').removeAttr('disabled');
        }).fail(function(jqXHR) {
            var responseCode = jqXHR.status;
            if(responseCode == 412) {
                var response = $.parseJSON(jqXHR.responseText);
                swal('Validation error', response.messages[0].message, 'warning');
            } else {
                swal('Error', 'There was a problem updating speaker, please contact admin.', 'warning');
            }
            form.find(':submit').removeAttr('disabled');
        });
    }
});