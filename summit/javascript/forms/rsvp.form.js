// RSVP

function jqueryValidatorInvalidHandler(form, validator) {
    var errors = validator.numberOfInvalids();
    if (errors) {
        var first_error  = $(validator.errorList[0].element);
        if(!first_error.is(':visible')){
            first_error = first_error.closest(":visible" );
        }
        $('html, body').animate({
            scrollTop: first_error.offset().top
        }, 2000);
    }
}

$(document).ready(function () {

    $('.rsvp_form').validate({
        errorPlacement: function (error, element) {
            error.insertAfter($(element).closest('div'));
        },
        invalidHandler: jqueryValidatorInvalidHandler,
    });

    $(document).off("click", ".rsvp_submit").on("click", ".rsvp_submit", function (e) {
        e.preventDefault();
        var btn = $(this);
        var form = btn.closest('.rsvp_form');
        if (!form.valid()) return false;

        var event_id = $('input[name="event_id"]', form).val();
        var summit_id = $('input[name="summit_id"]', form).val();
        var security_id = $('input[name="SecurityID"]', form).val();

        addRSVP(form, event_id, summit_id, security_id);

        return false;
    });

    if($('.rsvp_form').length > 0) {
        $('html, body').animate({
            scrollTop: $('.rsvp_form').offset().top - 100
        }, 1000);
    }

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
            $('.rsvp_delete', modal_id).removeClass('hidden');

            if(!loadedFromAjaxRequest) {
                swal(
                    {
                        title: "Done!",
                        text:"Your rsvp to this event was sent successfully.",
                        type: "success"
                    }, function() {
                        var url = new URI(window.location);
                        if (url.hasQuery("BackURL")) {
                            window.location = url.query(true)['BackURL'];
                        }
                    });
            }
            else{
                // close modal
                var modal = $('#rsvpModal');
                if(modal.length > 0)
                    modal.modal('hide');
            }
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