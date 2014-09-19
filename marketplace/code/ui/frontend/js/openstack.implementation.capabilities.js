(function( $ ){

    var table = null;
    var settings = {};

    var methods = {
        init: function(options){
            settings = $.extend({}, options );
            table = $(this);
            if(table.length > 0){
                $('td.coverage',table).each(function(index, value){
                    var coverage = parseInt(settings.coverages[index]);
                    var td = $(this);
                    var level = 'full';
                    if(coverage==0)
                        level = 'none';
                    else if(coverage>0 && coverage <= 50)
                        level = 'partial';
                    td.append('<span class="level-'+level+'">'+level+'</span>');
                });
            }
        }
    };

    $.fn.capabilities_meter = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.capabilities_meter' );
        }
    };
    // End of closure.
}( jQuery ));

