jQuery(document).ready(function($) {

	//Default Action
	$(".tabContent").hide(); //Hide all content
	$("ul.tabs li.active").show(); //Activate first tab
	var activeTab = $("ul.tabs li.active:first").find("a").attr("href");
	$(activeTab).show(); //Show first tab content
	
	//On Click Event
	
	$("ul.tabs li").click(function() {

		//Keep the height of the tab from adjusting and causing page scroll
		$("div.tabSet").css('height', $(this).closest("div.tabSet").height());
		$("div.tabSet").css('overflow', 'hidden');
	
		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tabContent").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		
		//Reset the tabs to a natural height
		$("div.tabSet").css('height', 'auto'); 
		$("div.tabSet").css('overflow', 'visible');
		
		//Keep the click from bubbling up
		return false;
	});
	
	
});
