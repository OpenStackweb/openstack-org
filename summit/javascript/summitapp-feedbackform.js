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

    var form_id  = 'SummitEventFeedbackForm_SummitEventFeedbackForm';
    var form     = $('#'+form_id);

    //validation
    /*form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            comment : { required: true , ValidPlainText:true, maxlength: 500 },
            rating  : { required: true , number:true}
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
            error.insertAfter(element);
        }
    });*/

    $("#SummitEventFeedbackForm_SummitEventFeedbackForm_rating").rating({size:'xs',showCaption:false,showClear:false});


    // SAVE REVIEW

    form.submit(function( event ) {
        event.preventDefault();

        //if(!form.valid()) return false;
        var event_id = $('#event_id').val();
        var security_id = $('#'+form_id+'_SecurityID',form).val();
        var url     = 'api/v1/summitschedule/'+event_id+'/add-feedback?SecurityID='+security_id;

        var request = {
            rating  :  $('#'+form_id+'_rating',form).val(),
            comment : $('#'+form_id+'_comment',form).val(),
            field_98438688 : $('#'+form_id+'_field_98438688',form).val()
        };

        $.ajax({
            type: 'PUT',
            url: url,
            data: JSON.stringify(request),
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