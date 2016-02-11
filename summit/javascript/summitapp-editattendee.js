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


    $("#aff_from").datetimepicker();
    $("#aff_to").datetimepicker();


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
                    return $.getJSON('api/v1/summits/'+summit_id+'/members/options',{query:query});
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
        var summit_id = $('#summit_id').val();
        $.getJSON('api/v1/summits/'+summit_id+'/attendees/member/'+event.item.id, {}, function(data){
            if (data.speaker) {
                var title = (data.speaker.Title) ? data.speaker.Title : '';
                var first_name = (data.speaker.FirstName) ? data.speaker.FirstName : '';
                var last_name = (data.speaker.LastName) ? data.speaker.LastName : '';
                var bio = (data.speaker.Bio) ? data.speaker.Bio : '';

                $('#title').val(title);
                $('#first_name').val(first_name);
                $('#last_name').val(last_name);
                tinyMCE.get('bio').setContent(bio);

                $('.speaker_details').show();
                $('.no_speaker').hide();
            } else {
                $('.no_speaker_msg').html('This member is not a speaker.');

                $('.speaker_details').hide();
                $('.no_speaker').show();
            }

            $('#aff_company').tagsinput('removeAll');
            $('#aff_from').val('');
            $('#aff_to').val('');
            $('#aff_current').attr('checked',false);
            
            if (data.affiliation) {
                $('#aff_company').tagsinput('add', data.affiliation.Company);
                $('#aff_from').val(data.affiliation.StartDate);
                $('#aff_to').val(data.affiliation.EndDate);
                $('#aff_current').attr('checked',data.affiliation.Current);
            }

            $('.affiliation').show();
            $('.no_affiliation').hide();

        });
    });

    $('#member').on('itemRemoved', function(event) {
        $('.no_speaker_msg').html('No member selected');
        $('.speaker_details').hide();
        $('.no_speaker').show();

        $('.affiliation').hide();
        $('.no_affiliation').show();
    });

    $('#aff_company').tagsinput({
        itemValue: "id",
        itemText: "name",
        freeInput: false,
        maxTags: 1,
        typeahead: {
            minLength: 3,
            items: 'all',
            source: function(query) {
                var max_reached = $('.bootstrap-tagsinput','.affiliation').hasClass('bootstrap-tagsinput-max');
                if (!max_reached) {
                    var summit_id = $('#summit_id').val();
                    return $.getJSON('api/v1/summits/'+summit_id+'/attendees/company_options',{query:query});
                } else {
                    return false;
                }
            }
        }
    });

    // if we already have a company assigned to this attendee
    if (!$.isEmptyObject(company)) {
        $('#aff_company').tagsinput('add', company);
    }

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

    form.submit(function (event) {
        event.preventDefault();

        var summit_id = $('#summit_id').val();
        var attendee_id = $('#attendee_id').val();
        var url = 'api/v1/summits/'+summit_id+'/attendees/'+attendee_id+'/update';

        var request = {
            member: $('#member').val(),
            aff_company: $('#aff_company').val(),
            aff_from: $('#aff_from').val(),
            aff_to: $('#aff_to').val(),
            aff_current: ($('#aff_current').prop('checked')) ? 1 : 0,
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

