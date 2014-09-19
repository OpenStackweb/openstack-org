jQuery(document).ready(function($) {

    var form = $("#EventRegistrationRequestForm_EventRegistrationRequestForm");
    var form_validator = null;

    if(form.length > 0){

        //main form validation
        form_validator = form.validate({
            onfocusout: false,
            focusCleanup: true,
            rules: {
                point_of_contact_name    : { required: true , ValidPlainText:true, maxlength: 100 },
                point_of_contact_email   : { required: true , email:true, maxlength: 100 },
                title      : { required: true , ValidPlainText:true, maxlength: 35 },
                url        : {required: true, url: true, maxlength: 255},
                city       : {required: true, ValidPlainText: true, maxlength: 255},
                country    : {required: true},
                start_date  : {required: true, dpDate: true},
                end_date    : {required: true, dpDate: true, dpCompareDate:'ge #EventRegistrationRequestForm_EventRegistrationRequestForm_start_date'}
            },
            focusInvalid: false,
            invalidHandler: function(form, validator) {
                if (!validator.numberOfInvalids())
                    return;
                var element = $(validator.errorList[0].element);
                if(!element.is(":visible")){
                    element = element.parent();
                }

                $('html, body').animate({
                    scrollTop: element.offset().top
                }, 2000);
            },
            errorPlacement: function(error, element) {
                if(!element.is(":visible")){
                    element = element.parent();
                }
                error.insertAfter(element);
            }
        });
        // initialize widgets

        var date_picker_start = $('#EventRegistrationRequestForm_EventRegistrationRequestForm_start_date',form);
        date_picker_start.datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0
        });

        var date_picker_end = $('#EventRegistrationRequestForm_EventRegistrationRequestForm_end_date',form);
        date_picker_end.datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0
        });

        $('#EventRegistrationRequestForm_EventRegistrationRequestForm_country',form).chosen({
            disable_search_threshold: 10,
            width: '315px'
        });
        $('#EventRegistrationRequestForm_EventRegistrationRequestForm_country',form).change(function () {
            form_validator.resetForm();
        });
    }
});