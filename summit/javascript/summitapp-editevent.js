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

var form_validator = null;

$(document).ready(function(){

    $('#summit_type').chosen({width: "100%", placeholder_text_multiple: "Select Summit Types..."});

    $("#start_date").datetimepicker();
    $("#end_date").datetimepicker();

    // Speakers autocomplete tags
    $('#speakers').tagsinput({
        itemValue: "id",
        itemText: "name",
        freeInput: false,
        typeahead: {
            minLength: 4,
            items: 'all',
            source: function(query) {
                var summit_id = $('#summit_id').val();
                var event_id = $('#event_id').val();
                return $.getJSON('api/v1/summits/'+summit_id+'/members/options',{query:query});
            }
        }
    });

    $.each(speakers, function(index, value) {
        $('#speakers').tagsinput('add', value);
    });

    // Tags autocomplete tag
    $('#tags').tagsinput({
        itemValue: "id",
        itemText: "name",
        freeInput: false,
        tagClass: 'label label-default',
        typeahead: {
            minLength: 3,
            items: 'all',
            source: function(query) {
                var summit_id = $('#summit_id').val();
                var event_id = $('#event_id').val();
                return $.getJSON('api/v1/summits/'+summit_id+'/events/'+event_id+'/get_tags',{query:query});
            }
        }
    });

    $.each(tags, function(index, value) {
        $('#tags').tagsinput('add', value);
    });

    // Sponsors autocomplete tag
    $('#sponsors').tagsinput({
        itemValue: "id",
        itemText: "name",
        freeInput: false,
        tagClass: 'label label-success',
        typeahead: {
            minLength: 3,
            items: 'all',
            source: function(query) {
                var summit_id = $('#summit_id').val();
                var event_id = $('#event_id').val();
                return $.getJSON('api/v1/summits/'+summit_id+'/events/'+event_id+'/get_sponsors',{query:query});
            }
        }
    });

    $.each(sponsors, function(index, value) {
        $('#sponsors').tagsinput('add', value);
    });

    tinymce.init({
        selector: "textarea",
        width:      '100%',
        height:     270,
        plugins:    [ "anchor link" ],
        statusbar:  false,
        menubar:    false
    });

    var form = $('#edit-event-form');

    //validation
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            title: {required: true},
            description: {required: true}
        },
        focusInvalid: false,
        invalidHandler: function (form, validator) {
            if (!validator.numberOfInvalids())
                return;
            var element = $(validator.errorList[0].element);
            if (!element.is(":visible")) {
                element = element.parent();
            }
        },
        errorPlacement: function (error, element) {
            if (!element.is(":visible")) {
                element = element.parent();
            }
            //error.insertAfter(element);
        }
    });

    form.submit(function (event) {
        event.preventDefault();

        if (!form.valid()) return false;
        var summit_id = $('#summit_id').val();
        var event_id = $('#event_id').val();
        var url = 'api/v1/summits/'+summit_id+'/events/'+event_id+'/update';

        var request = {
            title: $('#title').val(),
            description: $('#description').val(),
            location_id: $('#location').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            event_type: $('#event_type').val(),
            summit_type: $('#summit_type').val(),
            allow_feedback: ($('#allow_feedback').prop('checked')) ? 1 : 0,
            tags: $('#tags').val(),
            sponsors: $('#sponsors').val(),
            speakers: $('#speakers').val()
        };

        $.ajax({
            type: 'PUT',
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).done(function() {
            swal("Updated!", "Your event was updated successfully.", "success");
        }).fail(function(jqXHR) {
            var responseCode = jqXHR.status;
            if(responseCode == 412) {
                var response = $.parseJSON(jqXHR.responseText);
                swal('Validation error', response.messages[0], 'warning');
            }
        });

        return false;
    });

});

