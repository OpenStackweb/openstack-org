	jQuery(function($) {
		$('.folder, .folder>a').click(function(e) {
			var div, folder_cont, offset_arrow;
			if ($(e.target).parents('.folder-contents').length) {
				e.stopPropagation();
				return true;
			}
			$('.folder-contents').fadeOut();
			if ($(this).children('.folder-contents').length>0) {
				div = $(this);
			} else {
				div = $(this).parent();
			}
			folder_cont = div.children('.folder-contents');
            if(folder_cont.length>0){
                if (folder_cont.is(':visible')) {
                    // folder_cont.fadeOut();
                } else {
                    folder_cont.fadeIn();
                }
                offset_arrow = div.position().left + 55;
                div.children('.folder-contents').children('.arrow').css({
                    'margin-left' : offset_arrow + 'px'
                });
            }
			return false;
		});
		$('body').click(function(e) {
			$('.folder-contents').fadeOut();
		});
		$('.close').click(function(e) {
			$(this).parent().parent().fadeOut();
			return false;
		})
	});
