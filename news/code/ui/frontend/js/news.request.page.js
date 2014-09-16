jQuery(document).ready(function($){
    $('#add-new-article').click(function(event){
        window.location = $(this).attr('data-url');
    });

});