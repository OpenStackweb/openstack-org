// OpenStack In The Enterprise Affix
var num = 96; //number of pixels before modifying styles

$(window).bind('scroll', function () {
    if ($(window).scrollTop() > num) {
        $('.enterprise-bar').addClass('fixed');
    } else {
        $('.enterprise-bar').removeClass('fixed');
    }
});

// Smooth scroll
$('a').click(function(){
    $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top
    }, 500);
    return false;
});