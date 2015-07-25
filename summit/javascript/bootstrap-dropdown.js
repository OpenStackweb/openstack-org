(function( $ ){

    $(document).ready(function(){
        $('.dropdown-menu a').click(function(event){
            var option = $(this);
            event.preventDefault();
            var div = option.parent().parent().parent();
            div.toggleClass('open');
            var button = $('.dropdown-toggle', div);
            button.html(option.text()+'&nbsp;<span class="caret"></span>');
            return false;
        });
    });
    // End of closure.
}(jQuery ));