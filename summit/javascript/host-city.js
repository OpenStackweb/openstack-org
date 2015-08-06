// City nav active on scroll


$(document).ready(function () {
    var num = $('#nav-bar').offset().top;
    //$(document).on("scroll", onScroll);

    //smoothscroll
    $('a[href^="#"]').on('click', function (e) {
        e.preventDefault();
        $(document).off("scroll");
        $('a').each(function () {
            $(this).removeClass('active');
        })
        $(this).addClass('active');

        var target = this.hash,
            menu = target;
        $target = $(target);

        var detla = 0;

        // figure out how much room to allow for nav bar
        if ($('#nav-bar').hasClass('fixed')) {
            detla = 60;
        } else {
            detla = 170;
        }

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top - detla
        }, 500, 'swing', function () {
            window.location.hash = target;
        });
    });
    $(document).on("scroll", onScroll);


    $(window).bind('scroll', function () {
        if ($(window).scrollTop() > num) {
            $('.city-nav.city').addClass('fixed');
        } else {
            $('.city-nav.city').removeClass('fixed');
        }
    });
});

function onScroll(event) {
    var scrollPos = $(document).scrollTop();
    $('.city-nav a').each(function () {
        var currLink = $(this);
        var refElement = $(currLink.attr("href"));
        if (refElement.position().top - 60 <= scrollPos && refElement.position().top + refElement.outerHeight() > scrollPos) {
            $('.city-nav ul li a').removeClass("active");
            currLink.addClass("active");
        }
        else {
            currLink.removeClass("active");
        }
    });
}

