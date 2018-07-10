var currentSection = null;

function scrollFunction() {
    if ($(window).scrollTop() > 50) {
        $('#btn-top').show();
    } else {
        $('#btn-top').hide();
    }

    $('.section').each(function(){
        if ($(window).scrollTop() > ($(this).offset().top - 300)){
            $('.dropdown-toggle', '#paper-nav').html($(this).find('.title').html());
            currentSection = $(this).attr('id');
        }
    });

    if (currentSection == $('.section').first().attr('id')) {
        $('#btnPrv').prop('disabled', true);
        $('#btnNxt').prop('disabled', false);
    } else if (currentSection == $('.section').last().attr('id')) {
        $('#btnPrv').prop('disabled', false);
        $('#btnNxt').prop('disabled', true);
    } else {
        $('#btnPrv').prop('disabled', false);
        $('#btnNxt').prop('disabled', false);
    }

}

function topFunction() {
    $('html, body').animate({
        scrollTop: 0
    }, 1000);
}

// Close the Modal
function closeModal() {
    $('#lightbox').hide();
}

function scrollToSection(target, offset) {

    $('html, body').animate({
        scrollTop: $("#"+target).offset().top - offset - 50
    }, 1500);
}


$(document).ready(function(){

    currentSection = $('.section').first().attr('id');

    $('.nav-link').click(function(ev){
        ev.preventDefault();
        var target = $(this).data('target');
        var offset = $('.navigation').hasClass('affix') ? 0 : ($('.navigation').offset().top);

        scrollToSection(target, offset);
    });

    $('.stick-top').affix({
        offset: { top: 400 }
    });

    $('#btnPrv').click(function(ev){
        var prev_section = $('#'+currentSection).prev().attr('id');
        scrollToSection(prev_section, 0);
    });

    $('#btnNxt').click(function(ev){
        var next_section = $('#'+currentSection).next().attr('id');
        scrollToSection(next_section, 0);
    });

    $('#case-studies-btn').click(function(ev){
        ev.preventDefault();
        var sectionId =  $(this).attr('data-section');
        scrollToSection(sectionId, 0);
    });

    $('.dropdown-toggle', '#paper-nav').click(function () {
        $('.navigation').addClass('highlighted')
    });

    $(window).click(function () {
        $('.navigation').removeClass('highlighted');
    });

    $('.clickable-image').click(function(){
        $('#lightbox').hide();

        $('img', '#lightbox').attr('src', $(this).attr('src'));

        $('#lightbox').show();
    });

    window.onscroll = function () { scrollFunction() };
});