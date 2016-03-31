// City Nav Affix
var num = 400; //number of pixels before modifying styles

$(window).bind('scroll', function () {
    if ($(window).scrollTop() > num) {
        $('.guide-nav').addClass('fixed');
    } else {
        $('.guide-nav').removeClass('fixed');
    }
});