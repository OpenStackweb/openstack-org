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

    var form_id  = 'BootstrapForm_RSVPForm';
    var form     = $('#'+form_id);

    //validation
    //var form = $(".rsvp_form");
    form.validate({
        errorPlacement: function(error, element) {
            error.insertAfter($(element).closest('div'));
        }
    });

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
            data: JSON.stringify(form.serializeObject()),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                $('#rsvpModal').modal('toggle');
                swal("Done!", "Your rsvp to this event was sent successfully.", "success");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#rsvpModal').modal('toggle');
                swal('Error', 'There was a problem sending the rsvp, please contact admin.', 'warning');
            }
        });

        return false;
    });

});

(function ($) {
    $.fn.serializeObject = function()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
})(jQuery);