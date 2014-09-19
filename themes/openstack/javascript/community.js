jQuery(document).ready(function($) {

	// Add transitions for quotes
	$('#developerActivity').list_ticker({
		speed:4000,
		effect:'fade'
	});

	$('.addTip[title]').tooltip();
	
	$('#toggleButton').click(function() {
           if ($('#toggleSection').is(":hidden"))
           {
                $('#toggleSection').slideDown("slow");
           } else {
                $('#toggleSection').slideUp("slow");
           }
           return false;
	  });

});
