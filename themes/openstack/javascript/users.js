$(document).ready(function(){
	
	$('.rotator_old').bxSlider({
		auto: true,
		autoControls: true,
	});

	$('.rotator_new').bxSlider({
		auto: true,
		autoControls: true,
		buildPager: function(num){
			return $('ul.rotator_new li').eq(num+1).data('label');
		}
	});

	Shadowbox.init();
});

