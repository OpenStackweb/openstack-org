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

    tinymce.init({
        selector: "textarea",
        width:      '100%',
        height:     270,
        plugins:    [ "anchor link" ],
        statusbar:  false,
        menubar:    false
    });

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

    if (!$.isEmptyObject(member)) {
        $('#member_id').tagsinput('add', member);
    }

    var form = $('#edit-speaker-form');

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
        },
    });

    form.submit(function (evt) {
        evt.preventDefault();
        form.find('textarea').each(function() {
            var text_area = $(this);
            var text_editor = tinyMCE.get(text_area.attr('id'));
            if (text_editor)
                text_area.val(text_editor.getContent());
        });
        if (!form.valid()) return false;
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
        return false;
    });
});