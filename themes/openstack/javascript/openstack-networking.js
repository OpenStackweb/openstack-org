jQuery(document).ready(function($) {

	// JCAROUSEL ///////

	  var carouselOptions = {
	    auto: true,
	    visible: 2,
	    speed: 300,
	    pause: true,
	    btnPrev: function() {
	      return $(this).find('.prev');
	    },
	    btnNext: function() {
	      return $(this).find('.next');
	    }
	  };



	$('.slideshow').jCarouselLite(carouselOptions);


  $('a.screenshots').colorbox();
  $("a.vimeo").colorbox({iframe:true, innerWidth:800, innerHeight:530});
	
	
});
