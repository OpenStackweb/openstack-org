<script type="text/javascript">
$(document).ready(function() {

	//Default Action
	$(".tabContent").hide(); //Hide all content
	$("ul.tabs li.active").show(); //Activate first tab
	var activeTab = $("ul.tabs li.active:first").find("a").attr("href");
	$(activeTab).show(); //Show first tab content
	
	//On Click Event
	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tabContent").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		return false;
	});

	$('#members').click(function(){
	  window.location = '/community/';
	});
	
	$('#companies').click(function(){
	  window.location = '/community/companies/';
	});
	
	$('#boston-conference').click(function(){
	  window.location = 'http://essexdesignsummit.sched.org/';
	});		

});
</script>