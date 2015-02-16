(function ($) {
    $(document).ready(function () {
        
        $('input[name="filter"]').focus();
        
        $('#results').hide();
        
        $('input[name="filter"]').search('.section-item', function (on) {
            on.all(function (results) {
                var size = results ? results.size() : 0
                $('#count').text(size + ' results');
            });

            on.reset(function () {
                $('#results').hide();
                $('.section-title').show();
                $('.section-item').show();
            });

            on.empty(function () {
                $('.section-title').hide();                
                $('#results').show();
                $('#none').show();
                $('.section-item').hide();
            });

            on.results(function (results) {
                $('.section-title').hide();
                $('.section-item').hide();
                $('#results').show();                
                $('#none').hide();
                results.show();
            });
        });
    });
})(jQuery);

// Fix sidebar menu on scroll
var num = 320; //number of pixels before modifying styles

$(window).bind('scroll', function () {
    if ($(window).scrollTop() > num) {
        $('.faq-sidebar').addClass('fixed');
    } else {
        $('.faq-sidebar').removeClass('fixed');
    }
});