
// Toggle sidebar nav
$(".open-panel").click(function(){
  $("body").toggleClass("openNav");
});

// Smooth scroll
$('a[href^=#]').click(function(){
    $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top
    }, 500);
    return false;
});

// Photo Credit Tooltip
$('.photo-credit').tooltip();

$('a[data-confirm]').on('click', function (e) {
    e.preventDefault();
    var $t = $(this);
    sweetAlert({
        title: $t.data('confirm'),
        text: $t.data('confirm-text'),
        showCancelButton: true,
        confirmButtonText: $t.data('confirm-ok') || "Yes",
        cancelButtonText: $t.data('confirm-cancel') || "Nope",
        confirmButtonColor: $t.data('confirm-color') || "#DD6B55", 
        closeOnConfirm: true,
        closeOnCancel: true
    }, function(isConfirmed) {
        if(isConfirmed) {
            window.location = $t.attr('href');
        }
    });
});

$(function() {
	if($('#PresentationForm_PresentationForm_ShortDescription').length) {
		var MAX_WORDS = 200;
		var WARNING_THRESHOLD = 150;

		setTimeout(function() {		
			tinyMCE.get('PresentationForm_PresentationForm_ShortDescription')
				   .on('keyup', debounce(function(e) {				   	
					    var body = this.getBody();
					    var text = tinyMCE.trim(body.innerText || body.textContent);
					    var words = text.split(/[\w\u2019\'-]+/).length-1;					    
					    var diff = MAX_WORDS - words;
					    var klass = '';
					 	if(diff > 0) {
							$('#word-count').text(diff + ' words remaining');
							if(diff < WARNING_THRESHOLD) {
								klass = 'warning';
							}
					 	}
					 	else  {
					 		$('#word-count').text('0 words remaining. Your content will be truncated!');
					 		klass = 'danger';
					 	}

					 	$('#word-count').attr('class', klass);

				   }, 250)
				);
		},500);
	}
});

function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};