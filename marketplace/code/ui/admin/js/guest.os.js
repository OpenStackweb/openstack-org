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
            var guest_os = [];
            //iterate over collection
            $(".guest-os-type:checked").each(function(){
                var checkbox      = $(this);
                var guest_id  = parseInt(checkbox.attr('value'));
                guest_os.push(guest_id)
            });
            return guest_os;
        },
        load: function(guest_os){
            for(var i in guest_os){
                $('#guest_os_type_'+guest_os[i],form).prop('checked',true);
            }
        }
    };

    $.fn.guest_os = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.guest_os' );
        }
    };

    //helper functions

// End of closure.
}( jQuery ));
