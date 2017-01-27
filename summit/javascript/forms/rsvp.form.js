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
        if(!form.valid()) return false;

        var event_id    = $('input[name="event_id"]',form).val();
        var summit_id   = $('input[name="summit_id"]',form).val();
        var security_id = $('input[name="SecurityID"]',form).val();
        var rsvp_id = $('input[name="rsvp_id"]',form).val();

        if (rsvp_id) {
            updateRSVP(form, rsvp_id, event_id, summit_id, security_id);
        } else {
            addRSVP(form, event_id, summit_id, security_id);
        }

        return false;
    });

    $(document).off("click", ".rsvp_delete").on( "click", ".rsvp_delete", function(e) {
        e.preventDefault();
        var btn     = $(this);
        var form    = btn.closest('.rsvp_form');
        if(!form.valid()) return false;

        var event_id    = $('input[name="event_id"]',form).val();
        var summit_id   = $('input[name="summit_id"]',form).val();
        var security_id = $('input[name="SecurityID"]',form).val();
        var rsvp_id = $('input[name="rsvp_id"]',form).val();

        if (rsvp_id) {
            swal({
                title: 'Are you sure?',
                text: "There might be no more vacancy when you try to rsvp again!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(function () {
                deleteRSVP(form, rsvp_id, event_id, summit_id, security_id);
            }).done();
        }

        return false;
    });
});


function addRSVP(form, event_id, summit_id, security_id) {
    var url         = `api/v1/summits/${summit_id}/schedule/${event_id}/rsvp?SecurityID=${security_id}`;
    var modal_id    = `#rsvpModal_${event_id}`;

    $.ajax({
        type: 'POST',
        url: url,
        data: JSON.stringify(form.serializeForm()),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (rsvp_id,textStatus,jqXHR) {
            if($(modal_id).length > 0)
                $(modal_id).modal('hide');
            else
                $('.rsvp-container').slideUp();

            $('#btn_rsvp_'+event_id).html('Edit RSVP');
            $('input[name="rsvp_id"]',form).val(rsvp_id);
            $('.rsvp_delete',modal_id).removeClass('hidden');

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
            } else {
                $('#remove_from_my_schedule').show();
                $('#add_to_my_schedule').hide();
            }

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
}

function updateRSVP(form, rsvp_id, event_id, summit_id, security_id) {
    var url         = `api/v1/summits/${summit_id}/schedule/${event_id}/rsvp/${rsvp_id}?SecurityID=${security_id}`;
    var modal_id    = `#rsvpModal_${event_id}`;

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

            swal("Done!", "Your rsvp to this event was updated successfully.", "success");
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
}

function deleteRSVP(form, rsvp_id, event_id, summit_id, security_id) {
    var url         = `api/v1/summits/${summit_id}/schedule/${event_id}/rsvp/${rsvp_id}?SecurityID=${security_id}`;
    var modal_id    = `#rsvpModal_${event_id}`;

    $.ajax({
        type: 'DELETE',
        url: url,
        data: JSON.stringify(form.serializeForm()),
        contentType: "application/json; charset=utf-8",
        dataType: "text",
        success: function (data,textStatus,jqXHR) {
            $('input[name="rsvp_id"]',form).val('');
            $('.rsvp_delete',modal_id).addClass('hidden');
            form.trigger("reset");
            form.find('input:text, input:password, input:file, select, textarea').val('');
            form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');

            if($(modal_id).length > 0)
                $(modal_id).modal('hide');
            else
                $('.rsvp-container').slideUp();

            if (data == 'Regular') {
                $('#btn_rsvp_'+event_id).html('RSVP to this Event');
            } else if (data == 'WaitList') {
                $('#btn_rsvp_'+event_id).html('RSVP Waitlist to this Event');
            } else {
                $('#btn_rsvp_'+event_id).html('Event Full');
                $('#btn_rsvp_'+event_id).removeClass('btn-primary').addClass('btn-warning');
                $('#btn_rsvp_'+event_id).prop('disabled',true);
            }

            swal("Done!", "Your rsvp to this event was deleted.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(modal_id).modal('hide');
            var responseCode = jqXHR.status;
            if(responseCode == 412) {
                var response = $.parseJSON(jqXHR.responseText);
                swal('Validation error', response.messages[0].message, 'warning');
            } else {
                swal('Error', 'There was a problem deleting the rsvp, please contact admin.', 'warning');
            }

        }
    });
}
