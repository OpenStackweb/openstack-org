// RSVP

$(document).ready(function () {
    $('.rsvp_form').validate({
        errorPlacement: function(error, element) {
            error.insertAfter($(element).closest('div'));
        }
    });

    $(document).off("click", ".rsvp_submit").on( "click", ".rsvp_submit", function(e) {
        e.preventDefault();
        var btn     = $(this);
        var form    = btn.closest('.rsvp_form');
        var form_id = form.attr('id');
        console.log('rsvp form_id '+form_id);
        if(!form.valid()) return false;

        var event_id    = $('input[name="event_id"]',form).val();
        var summit_id   = $('input[name="summit_id"]',form).val();
        var security_id = $('input[name="SecurityID"]',form).val();
        var url         = 'api/v1/summits/'+summit_id+'/schedule'+'/'+event_id+'/rsvp?SecurityID='+security_id;
        var modal_id    = '#rsvpModal_'+event_id;

        $.ajax({
            type: 'PUT',
            url: url,
            data: JSON.stringify(form.serializeForm()),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                if($(modal_id).length > 0)
                    $(modal_id).modal('hide');
                else
                    $('.rsvp-container').slideUp();
                $('#btn_rsvp_'+event_id).attr('disabled','disabled');
                var span_container = $("#event_myschedule_action_"+event_id);

                if(span_container.length > 0){
                    span_container.attr('title','remove from my schedule');
                    span_container.removeClass();
                    span_container.addClass('icon-event-action own');
                    var icon = $('.icon-foreign-event', span_container);
                    if(icon.length > 0) {
                        icon.removeClass();
                        icon.addClass('fa fa-plus-circle myschedule-icon icon-own-event');
                    }
                }
                else
                    setAdded2MySchedule();

                swal("Done!", "Your rsvp to this event was sent successfully.", "success");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(modal_id).modal('hide');
                var responseCode = jqXHR.status;
                if(responseCode == 412) {
                    var response = $.parseJSON(jqXHR.responseText);
                    swal('Validation error', response.messages[0].message, 'warning');
                } else {
                    swal('Error', 'There was a problem sending the rsvp, please contact admin.', 'warning');
                }

            }
        });

        return false;
    });
});
