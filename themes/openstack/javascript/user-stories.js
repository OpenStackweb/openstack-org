// Show User Stories hero video

  $("#user-stories-video-trigger").click(function () {
    $('.user-stories-video-wrapper').addClass('on');
    $(this).addClass('off');
    event.preventDefault();
  });