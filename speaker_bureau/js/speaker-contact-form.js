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

    var form_id  = 'SpeakerContactForm_SpeakerContactForm';
    var form     = $('#'+form_id);

    //validation
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            org_name : { required: true , maxlength: 200 },
            org_email  : { required: true , email: true, maxlength: 200 },
            event_name  : { required: true , maxlength: 200 },
            event_format  : { required: true , maxlength: 200 },
            event_attendance  : { required: true , number: true },
            event_date  : { required: true , maxlength: 200 },
            event_location  : { required: true , maxlength: 200 },
            event_topic  : { required: true , maxlength: 200 },
            general_request  : { required: true }

        },
        focusInvalid: false,
        invalidHandler: function(form, validator) {
            if (!validator.numberOfInvalids())
                return;
            var element = $(validator.errorList[0].element);
            if(!element.is(":visible")){
                element = element.parent();
            }
        },
        errorPlacement: function(error, element) {
            if(!element.is(":visible")){
                element = element.parent();
            }
            //error.insertAfter(element);
        }
    });


    // SEND EMAIL

    form.submit(function( event ) {
        event.preventDefault();

        $('#'+form_id+'_action_sendSpeakerEmail',form).prop('disabled',true);

        if(!form.valid()) return false;
        var speaker_id = $('#'+form_id+'_speaker_id',form).val();
        var security_id = $('#'+form_id+'_SecurityID',form).val();
        var url     = 'api/v1/speaker-bureau/email?SecurityID='+security_id;

        var request = {
            speaker_id : speaker_id,
            org_name : $('#'+form_id+'_org_name',form).val(),
            org_email  : $('#'+form_id+'_org_email',form).val(),
            event_name  : $('#'+form_id+'_event_name',form).val(),
            event_format  : $('#'+form_id+'_event_format',form).val(),
            event_attendance  : $('#'+form_id+'_event_attendance',form).val(),
            event_date  : $('#'+form_id+'_event_date',form).val(),
            event_location  : $('#'+form_id+'_event_location',form).val(),
            event_topic  : $('#'+form_id+'_event_topic',form).val(),
            general_request  : $('#'+form_id+'_general_request',form).val(),
            field_98438688 : $('#'+form_id+'_field_98438688',form).val()
        };

        $.ajax({
            type: 'PUT',
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                $('fieldset','.contact_form_div').html('<h4>Thank you for contacting this speaker. Moving forward all communication will occur directly with the Speaker and not within OpenStack.org</h4>');

            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });

        return false;
    });



});