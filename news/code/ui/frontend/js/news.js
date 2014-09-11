jQuery(document).ready(function($){
    $('.slider').bxSlider({
        auto: true,
        autoControls: true,
        buildPager: function(num){
            return $('ul.slider li').eq(num+1).data('label');
        }
    });

    $('.featured').bxSlider({
        captions: true
    });

    Shadowbox.init();
});
