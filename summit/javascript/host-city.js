$(document).ready(function() {
    $(document).on("scroll", onScroll);
    $('a[href^="#"]').on("click", function(e) {
        e.preventDefault();
        $(document).off("scroll");
        $("a").each(function() {
            $(this).removeClass("active")
        });
        $(this).addClass("active");
        var target = this.hash,
            menu = target;
        $target = $(target);
        var detla = 0;
        if ($("#nav-bar").hasClass("fixed")) {
            detla = 60
        } else {
            detla = 170
        }
        $("html, body").stop().animate({
            scrollTop: $target.offset().top - detla
        }, 500, "swing", function() {
            window.location.hash = target;
            $(document).on("scroll", onScroll)
        })
    })
});

function onScroll(event) {
    var scrollPos = $(document).scrollTop();
    $(".city-nav a").each(function() {

    })
}

$(function() {
    $('[data-toggle="tooltip"]').tooltip()
});

var num = 980;
$(window).bind("scroll", function() {
    if ($(window).scrollTop() > num) {
        $(".city-nav.city").addClass("fixed")
    } else {
        $(".city-nav.city").removeClass("fixed")
    }
});