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

jQuery(document).ready(function($){

    var form_id  = 'HoneyPotForm_RSVPForm';
    var form     = $('#'+form_id);

    //validation
    //var form = $(".rsvp_form");
    form.validate();

    // SAVE RSVP

    form.submit(function( event ) {
        event.preventDefault();
        if(!form.valid()) return false;

        var event_id = $('#'+form_id+'_event_id').val();
        var summit_id = $('#'+form_id+'_summit_id').val();
        var security_id = $('#'+form_id+'_SecurityID',form).val();
        var url = 'api/v1/summits/'+summit_id+'/schedule'+'/'+event_id+'/rsvp?SecurityID='+security_id;

        $.ajax({
            type: 'PUT',
            url: url,
            data: JSON.stringify(form.serializeArray()),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                //push feedback
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });

        return false;
    });



});