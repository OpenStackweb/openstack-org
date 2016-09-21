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

    $('#add_attendee_modal').on('hidden.bs.modal', function (e) {
        $('#add_attendee_member').tagsinput('removeAll');
    })

    var members_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/members?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#add_attendee_member').tagsinput({
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

function addAttendee(){
    var member_id = $('#add_attendee_member').val();
    $.ajax({
        url: 'api/v1/summits/'+summit_id+'/attendees/',
        type: 'POST',
        dataType: 'JSON',
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify({member_id: member_id}),
        success: function(message){
            $('#add_attendee_modal').modal("hide");
            swal('Success','Attendee added successfully.','success');
        },
        error: function(response,status,error) {
            swal('Validation error', response.responseJSON.messages[0].message, 'warning');
        }
    });

}