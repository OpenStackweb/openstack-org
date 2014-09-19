jQuery(document).ready(function($) {

  // Add transitions for quotes
  $('#quotes').list_ticker({
    speed:8000,
    effect:'fade'
  });

  $('a.screenshots').colorbox({rel:'gal'});
  $(".vimeo").colorbox({iframe:true, innerWidth:800, innerHeight:530});

});
