(function( $ ){

    var form     = null;
    var settings = {};

    var methods = {
        init: function(options){
            settings = $.extend({}, options );
            form = $(this);
            if(form.length>0){
                var form_validator = form.validate({
                    onfocusout: false,
                    focusCleanup: true
                });
            }
        },
        serialize:function (){
            var is_valid = form.valid();
            if(!is_valid) return false;
            var expertise_areas = [];
            //iterate over collection
            $(".expertise-area-checkbox:checked").each(function(){
                var checkbox      = $(this);
                expertise_areas.push(parseInt(checkbox.attr('data-expertise-area-id')));
            });
            return expertise_areas;
        },
        load: function(expertise_areas) {
            for(var i in expertise_areas){
                var id = expertise_areas[i];
                $('#expertise_area_'+id,form).prop('checked',true);
            }
        }
    };

    $.fn.expertise_areas = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.expertise_areas' );
        }
    };
    //helper functions
// End of closure.
}( jQuery ));

