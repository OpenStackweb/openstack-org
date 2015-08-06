$(".hotel-landing-select-btn").click(function () {
	$('.hotel-landing-choice').not(this).closest('.hotel-landing-choice').removeClass('active');
	$(this).closest('.hotel-landing-choice ').addClass('active');

    if ($('.hotel-landing-choice.in').hasClass('active')) {
        $('.inside-block').addClass('active');
        $('#in-block-btn').text('selected');
    } else {
        $('.inside-block').removeClass('active');
        $('#in-block-btn').text('select this option');
    }

    if ($('.hotel-landing-choice.out').hasClass('active')) {
        $('.outside-block').addClass('active');
        $('#out-block-btn').text('selected');
    } else {
        $('.outside-block').removeClass('active');
        $('#out-block-btn').text('select this option');
    }
	    event.preventDefault();
});

$(document).ready(function() {
   $('input[type="radio"]').click(function() {
       if($(this).attr('id') == 'emailRadio') {
            $('#paymentEmail').show();           
       }

       else {
            $('#paymentEmail').hide();   
       }
   });
});

$(document).ready(function() {
   $('input[type="radio"]').click(function() {
       if($(this).attr('id') == 'phoneRadio') {
            $('#paymentPhone').show();           
       }

       else {
            $('#paymentPhone').hide();   
       }
   });
});