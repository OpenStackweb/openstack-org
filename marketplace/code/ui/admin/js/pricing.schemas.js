(function( $ ){


    var form  = null;
    var table = null;
    var form_validator = null;
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
            var pricing_schemas = [];
            //iterate over collection
            $('.pricing-schema-checkbox:checked').each(function(){
                var checkbox      = $(this);
                pricing_schemas.push(parseInt(checkbox.attr('data-pricing-schema-id')))
            });
            return pricing_schemas;
        },
        load: function(pricing_schemas) {
            for(var i in pricing_schemas){
                var pricing_schema_id = pricing_schemas[i];
                $('#pricing_schema_'+pricing_schema_id,form).prop('checked',true);
            }
        }
    };


    $.fn.pricing_schemas = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.pricing_schemas' );
        }
    };

// End of closure.
}( jQuery ));