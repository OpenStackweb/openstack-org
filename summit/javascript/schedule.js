$("#mainschedule-btn").click(function () {

    $('#mainschedule').addClass('active');
    $(this).addClass('on').removeClass('off');;
    $('#designschedule').removeClass('active');
    $('#designschedule-btn').removeClass('on').addClass('off');

    event.preventDefault();
});

$("#designschedule-btn").click(function () {

    $('#designschedule').addClass('active');
    $(this).addClass('on').removeClass('off');
    $('#mainschedule').removeClass('active');
    $('#mainschedule-btn').removeClass('on').addClass('off');

    event.preventDefault();
});
