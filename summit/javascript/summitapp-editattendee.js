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

    // Member autocomplete
    $('#member').tagsinput({
        itemValue: "id",
        itemText: "name",
        freeInput: false,
        maxTags: 1,
        typeahead: {
            minLength: 4,
            items: 'all',
            source: function(query) {
                var max_reached = $('.bootstrap-tagsinput','.member_container').hasClass('bootstrap-tagsinput-max');
                if (!max_reached) {
                    var summit_id = $('#summit_id').val();
                    return $.getJSON('api/v1/summits/'+summit_id+'/member_options',{query:query});
                } else {
                    return false;
                }
            }
        }
    });

    // if we already have a member assigned to this attendee
    if (!$.isEmptyObject(member)) {
        $('#member').tagsinput('add', member);
    }

    $('#member').on('itemAdded', function(event) {
        $.getJSON('api/v1/summits/'+summit_id+'/member_speaker/'+event.item.id, {}, function(speaker){
            if (speaker) {
                $('#title').val(speaker.Title);
                $('#first_name').val(speaker.FirstName);
                $('#last_name').val(speaker.LastName);
                tinyMCE.get('bio').setContent(speaker.Bio);

                $('.speaker_details').show();
                $('.no_speaker').hide();
            } else {
                $('.no_speaker_msg').html('This member is not a speaker.');

                $('.speaker_details').hide();
                $('.no_speaker').show();
            }
        });
    });

    $('#member').on('itemRemoved', function(event) {
        $('.no_speaker_msg').html('No member selected');
        $('.speaker_details').hide();
        $('.no_speaker').show();
    });

    $('input','.bootstrap-tagsinput').keypress(function(e) {
        var max_reached = $(this).parents('.bootstrap-tagsinput').hasClass('bootstrap-tagsinput-max');
        if (max_reached) {
            e.preventDefault();
        }
    });

    tinymce.init({
        selector: "textarea",
        width:      '100%',
        height:     270,
        plugins:    [ "anchor link" ],
        statusbar:  false,
        menubar:    false
    });

    var form = $('#edit-attendee-form');

    //validation
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            title: {required: true},
            first_name: {required: true},
            last_name: {required: true},
            bio: {required: true}
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
        var attendee_id = $('#attendee_id').val();
        var url = 'api/v1/summits/'+summit_id+'/attendees/'+attendee_id+'/update';

        var request = {
            member: $('#member').val(),
            company: $('#company').val(),
            share_info: ($('#share_info').prop('checked')) ? 1 : 0,
            checked_in: ($('#checked_in').prop('checked')) ? 1 : 0,
            title: $('#title').val(),
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            bio: tinyMCE.get('bio').getContent()
        };

        $.ajax({
            type: 'PUT',
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).done(function() {
            swal("Updated!", "Attendee was updated successfully.", "success");
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

