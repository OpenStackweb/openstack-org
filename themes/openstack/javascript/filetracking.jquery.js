(function( $ ){

    var filetypes = /[\./](zip|exe|pdf|doc*|xls*|ppt*|mp3)$/i;

    var methods = {
        init : function(options) {
            var baseHref = '';
            if ($('base').attr('href') != undefined)
                baseHref = $('base').attr('href');
                $('a').each(function() {
                    var href = $(this).attr('href');
                    if (href && href.match(filetypes)) {
                            $(this).click(function() {
                            var extension = (/[\./]/.exec(href)) ? /[^\./]+$/.exec(href) : undefined;
                            var filePath = href;
                            _gaq.push(['_trackEvent', 'Download', 'Click-' + extension, filePath]);
                            if ($(this).attr('target') != undefined && $(this).attr('target').toLowerCase() != '_blank') {
                                setTimeout(function() { location.href = baseHref + href; }, 200);
                                return false;
                            }
                        });
                    }
             });
        }
    };

    $.fn.filetracking = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.filetracking' );
        }
    };

    //helper functions

// End of closure.
}( jQuery));