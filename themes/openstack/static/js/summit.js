
// Toggle sidebar nav
$(".open-panel").click(function(){
  $("body").toggleClass("openNav");
});

// Smooth scroll
$('a').click(function(){
    $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top
    }, 500);
    return false;
});

// Photo Credit Tooltip
$('.photo-credit').tooltip()