
// Toggle sidebar nav
$(".open-panel").click(function(event){
   $("body").toggleClass("openNav");
    event.preventDefault();
    return false;
});

// Smooth scroll
$('a[href^=#]').click(function(e){
    var $this = $(this);
    e.preventDefault();
    $('html, body').animate({
        scrollTop: $($this.attr('href')).offset().top
    }, 500);
});

// Photo Credit Tooltip
$('.photo-credit').tooltip();

$('a[data-confirm]').on('click', function (e) {
    e.preventDefault();
    var $t = $(this);
    sweetAlert({
        title: $t.data('confirm'),
        text: $t.data('confirm-text'),
        showCancelButton: true,
        confirmButtonText: $t.data('confirm-ok') || "Yes",
        cancelButtonText: $t.data('confirm-cancel') || "Nope",
        confirmButtonColor: $t.data('confirm-color') || "#DD6B55", 
        closeOnConfirm: true,
        closeOnCancel: true
    }, function(isConfirmed) {
        if(isConfirmed) {
            window.location = $t.attr('href');
        }
    });
});

var MAX_WORDS = 450;
var WARNING_THRESHOLD = 150;

$(function() {
	if($('#PresentationForm_PresentationForm_ShortDescription').length) {


		setTimeout(function() {		
			tinyMCE.get('PresentationForm_PresentationForm_ShortDescription')
				   .on('keyup', debounce(textEvent, 250))
                   .on('paste', textEvent);
		},500);
	}

    $('.delete-speaker').click(function(event){
        if(!confirm('are you sure?')){
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    });

    if($('#BootstrapForm_AddSpeakerForm_SpeakerType_Me').length == 0){
        $('#legal-other').show();
        $('#legal-me').hide();
    }
    else if($('#BootstrapForm_AddSpeakerForm_SpeakerType_Me').is(':checked') ){
        $('#legal-other').hide();
        $('#legal-me').show();
    }

    $('#BootstrapForm_AddSpeakerForm_SpeakerType_Else').click(function(evt){
       if($(this).is(':checked')){
           $('#legal-other').show();
           $('#legal-me').hide();
       }
    });

    $('#BootstrapForm_AddSpeakerForm_SpeakerType_Me').click(function(evt){
        if($(this).is(':checked')){
            $('#legal-other').hide();
            $('#legal-me').show();
        }
    });
});

function textEvent(editor, event) {
    var body = this.getBody();
    var text = tinyMCE.trim(body.innerText || body.textContent);
    var words = text.split(/[\w\u2019\'-]+/).length-1;
    var diff = MAX_WORDS - words;
    var klass = '';
    if(diff > 0) {
        $('#word-count').text(diff + ' words remaining');
        if(diff < WARNING_THRESHOLD) {
            klass = 'warning';
        }
    }
    else  {
        $('#word-count').text('0 words remaining. Your content will be truncated!');
        klass = 'danger';
    }

    $('#word-count').attr('class', klass);

}

function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};