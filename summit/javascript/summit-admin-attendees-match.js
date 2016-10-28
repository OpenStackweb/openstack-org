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

    //MEMBER AUTOCOMPLETE  --------------------------------------------//
    var members_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/members?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#match_attendee_member').tagsinput({
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

    $('#match_attendee_member').on('itemAdded', function(event) {
        $('input','#match_attendee_suggestions').prop('checked',false);
    });

    $('#match_button').click(function(){
        var eventbrite_attendee_id = $('#eventbrite_attendee_id').val();
        var member_id = $('#match_attendee_member').val();
        var suggestion = $('.match_suggestion:checked').val();

        if (!member_id && !suggestion) return false;

        var match_id = suggestion || member_id ;
        var url        = 'api/v1/summits/'+summit_id+'/attendees/match/'+eventbrite_attendee_id+'/'+match_id;

        $.ajax({
            type: 'POST',
            url: url,
            contentType: 'application/json; charset=utf-8',
            dataType: 'json'
        }).done(function(attendee_id) {
            var edit_attendee_url = base_url+'/'+summit_id+'/attendees/'+attendee_id;
            swal({
                    title: 'Success! Attendee matched.',
                    text: 'Edit Attendee <a href="'+edit_attendee_url+'">here</a>. ',
                    confirmButtonText: "Done!",
                    type: "success",
                    html: true
                },
                function() {
                    location.reload();
                });
        });
    });


});
