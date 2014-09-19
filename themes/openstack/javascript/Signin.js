jQuery(function ($) {
    document.getElementById("SigninForm_SigninForm").reset();

    $(window).unload(function () {
        document.getElementById("SigninForm_SigninForm").reset();
    });

    $('form :input:visible:first').focus();

    $("#SuccessMessage").delay(1000).fadeOut("slow");

});