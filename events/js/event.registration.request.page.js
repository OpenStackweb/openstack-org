jQuery(document).ready(function($) {
    $('#add-new-event').click(function(event){
        window.location = $(this).attr('data-url');
    });
});
