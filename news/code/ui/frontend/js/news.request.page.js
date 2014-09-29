jQuery(document).ready(function($){
    $('.link_button').click(function(event){
        window.location = $(this).attr('data-url');
    });



});