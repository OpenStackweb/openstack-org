(function( $ ){

    var form  = null;
    var table = null;

    var methods = {
        init : function(options) {
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
            var hypervisors = [];
            //iterate over collection
            $(".hypervisor-type:checked").each(function(){
                var checkbox      = $(this);
                var hypervisor_id  = parseInt(checkbox.attr('value'));
                hypervisors.push(hypervisor_id)
            });
            return hypervisors;
        },
        load: function(hypervisors) {
            for(var i in hypervisors){
                $('#hypervisor-type_'+hypervisors[i],form).prop('checked',true);
            }
        }
    };

    $.fn.hypervisors = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.hypervisors' );
        }
    };

    //helper functions

// End of closure.
}( jQuery ));

