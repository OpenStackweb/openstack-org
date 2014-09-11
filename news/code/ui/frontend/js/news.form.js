jQuery(document).ready(function($){
    $('#NewsForm_NewsForm_slider').change(function(){
        if ($(this).attr("checked")){
            $('#NewsForm_NewsForm_featured').attr('disabled','disabled');
            $('#date_expire').removeClass('hidden').find('.hidden').removeClass('hidden');
            $('#image').removeClass('hidden');
        } else {
            $('#NewsForm_NewsForm_featured').removeAttr('disabled');
            $('#date_expire').addClass('hidden');
            $('#image').addClass('hidden');
        }
    });

    $('#NewsForm_NewsForm_featured').change(function(){
        if ($(this).attr("checked")){
            $('#NewsForm_NewsForm_slider').attr('disabled','disabled');
            $('#date_expire').removeClass('hidden').find('.hidden').removeClass('hidden');
            $('#image').removeClass('hidden');
        } else {
            $('#NewsForm_NewsForm_slider').removeAttr('disabled');
            $('#date_expire').addClass('hidden');
            $('#image').addClass('hidden');

        }
    });
});
