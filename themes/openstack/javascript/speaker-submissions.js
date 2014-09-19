jQuery(document).ready(function($) {

	// Add star ratings to each submission
	$('.rating').each(function()	{
		var submission = $(this).closest('div .submission').attr('id');
		var previousRating = $("input[id=" + submission + "]").val();
		$(this).raty({
			number: 3,
			cancel: true,
			cancelOn: 'undo.gif',
			cancelOff: 'undo.gif',
			cancelPlace: 'right',
			start: previousRating,
			path: '/themes/openstack/javascript/jquery.raty-2.1.0/img/',
			hintList:       ['maybe', 'want to see', 'must-see'],
			click: function(score, evt) {
		          $.ajax({
		           type: 'POST',
		           url: '/conference/san-francisco-2012/speaker-submissions/saverating/',
		           data: {'score':score, 'submission':submission},
		           success: function(data){ $("div.saved-rating#saved" + submission).show().animate({opacity: 1.0}, 1000).fadeOut(500); }         
		         }) 
		    }
		});
	});

	//hide comment boxes
	$('.commentArea').hide();
	
	// toggles comment area  
	$('a.toggleCommentArea').click(function() {
	   $(this).closest('div.submission').find('div.commentArea').slideToggle(400);
	   return false;
	});

	$('.commentSubmitButton').each(function()	{
		
		$(this).click(function() {
			var submission = $(this).closest('div .submission').attr('id');
			var comment = document.getElementById('comment' + submission).value;
		    $.ajax({
		           type: 'POST',
		           url: '/conference/san-francisco-2012/speaker-submissions/savecomment/',
		           data: {'comment':comment, 'submission':submission},
		           success: function(data){ $("div.saved-rating#saved" + submission).show().animate({opacity: 1.0}, 1000).fadeOut(500); }         
		         }) 
		    return false; // prevent normal submit
		});
	});
		

});



