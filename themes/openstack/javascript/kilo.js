// Close dedication
$(function() {                       
  $(".dedication-wrapper .fa-close").click(function() {
    $(".dedication-wrapper").toggleClass("dedication-close");
    $(".dedication-tab").toggleClass('opened');
  });
});

// Open dedication
$(function() {                       
  $("a.dedication-tab").click(function() {
    $(".dedication-wrapper").toggleClass("dedication-close");
    $(".dedication-tab").toggleClass('opened');
  });
});
