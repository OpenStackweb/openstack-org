jQuery(document).ready(function($) {
    $('#add-new-job').click(function(event){
        window.location = $(this).attr('data-url');
    });
});
