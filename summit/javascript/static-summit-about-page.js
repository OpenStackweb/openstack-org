$(document).ready(function() {
	inView.threshold(0.5);
	inView('#sponsorsCarousel')
		.on('enter', el => {
        	$(el).carousel('cycle')
		})
		.on('exit', el => {
        	$(el).carousel('pause')
		}
	);
});