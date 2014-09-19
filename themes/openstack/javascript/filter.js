jQuery(document).ready(function($) {

	//default each row to visible
	$('.filter li').addClass('visible');
	
	//overrides CSS display:none property
	//so only users w/ JS will see the
	//filter box
	$('#search').show();
	$('#search input').focus();
	
	$('#filter').keyup(function(event) {
		//if esc is pressed or nothing is entered
    if (event.keyCode == 27 || $(this).val() == '') {
			//if esc is pressed we want to clear the value of search box
			$(this).val('');
			
			//we want each row to be visible because if nothing
			//is entered then all rows are matched.
      $('.filter li').removeClass('visible').show().addClass('visible');
      $('.filter .groupHeading').removeClass('visible').show().addClass('visible');   
      $('#filterHeading').removeClass('visible').hide();

    }

		//if there is text, lets filter
		else {
      		  // Hide the group headings
      		  $('.filter .groupHeading').removeClass('visible').hide();

      		  // Show the filter heading
      		  $('#filterHeading').removeClass('visible').show().addClass('visible');   


    		  filter('.filter li', $(this).val());
    }

	});
	
});


//filter results based on query
function filter(selector, query) {
	query	=	$.trim(query); //trim white space
  query = query.replace(/ /gi, '|'); //add OR for regex
  
  $(selector).each(function() {
    ($(this).children('strong').text().search(new RegExp(query, "i")) < 0) ? $(this).hide().removeClass('visible') : $(this).show().addClass('visible');
  });
}