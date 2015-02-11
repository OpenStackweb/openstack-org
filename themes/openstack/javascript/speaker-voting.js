// Toggle sidebar nav
$(".voting-open-panel").click(function(){
  $("body").toggleClass("openVotingNav");
});

function checkWidth() {
	if ($(window).width() > 767) {
	    $('body').removeClass('openVotingNav');
	}
}

$(window).resize(checkWidth);


// Resize Presentations in Sidebar
$(document).ready(function(){
	resizeDiv();
});

window.onresize = function(event) {
	resizeDiv();
}

function resizeDiv() {
	vph = $(window).height() - 200;
	$(".presentation-list").css({"height": vph + "px"});
}


jQuery(function ($) {


	$('#voting-rate-single a').click(function (e) {
    	e.preventDefault();               // prevent default anchor behavior
   		var goTo = this.href;             // store anchor href

    	$('.current-vote').removeClass('current-vote');
    	$(this).addClass('current-vote');

	    setTimeout(function(){
	         window.location = goTo;
	    },1000);

	});

	$("p:empty").hide(); 


	// Bind the voting keys
	Mousetrap.bind('3', function() { window.location = $("#vote-3").attr("href"); });
	Mousetrap.bind('2', function() { window.location = $("#vote-2").attr("href"); });
	Mousetrap.bind('1', function() { window.location = $("#vote-1").attr("href"); });
	Mousetrap.bind('0', function() { window.location = $("#vote-0").attr("href"); });
	Mousetrap.bind('s', function() { window.location = $("#skip").attr("href"); });

});