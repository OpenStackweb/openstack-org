jQuery(document).ready(function($){

    var form = $("#NewsRequestForm_NewsRequestForm");
    var form_validator = null;

    if(form.length > 0){
        //main form validation
        form_validator = form.validate({
            onfocusout: false,
            focusCleanup: true,
            rules: {
                date  : {required: true, dpDate: true},
                date_embargo    : {required: true, dpDate: true, dpCompareDate:'ge #NewsRequestForm_NewsRequestForm_date'}
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


        var date_picker_start = $('#NewsRequestForm_NewsRequestForm_date',form);
        date_picker_start.datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0
        });

        var date_picker_end = $('#NewsRequestForm_NewsRequestForm_date_embargo',form);
        date_picker_end.datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0
        });
    }

});
