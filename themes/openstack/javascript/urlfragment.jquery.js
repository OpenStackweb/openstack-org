(function( $ ){

    var original_hash = '';
    var hash   = {};

    var methods = {
        init : function(options) {
            // This is the easiest way to have default options.
            var settings = $.extend({
                // These are the defaults.
                original_hash: window.location.hash
            }, options );

            hash = convertToHash(settings.original_hash);
        },
        getParam: function(key)
        {
            if(original_hash !==  window.location.hash){
                original_hash = window.location.hash;
                hash = convertToHash(original_hash);
            }

            if(!hash.hasOwnProperty(key) ) return null;
            return hash[key];
        },
        getParams: function(){
            if(original_hash !==  window.location.hash){
                original_hash = window.location.hash;
                hash = convertToHash(original_hash);
            }

            return hash;
        },
        setParam: function (key, value)
        {
            if(original_hash !==  window.location.hash){
                original_hash = window.location.hash;
                hash = convertToHash(original_hash);
            }
            if(value !== null && value !== '')
                hash[key] = value;
            else
                delete hash[key];
            return this;
        },
        serialize:function(){
            var res = '';
            for(var key in hash)
            {
                var val = hash[key];
                if(res !== '') res += '&';
                res += key+'='+val;
            }
            return res;
        }
    };

    //private methods

    function convertToHash(str_hash)
    {
        str_hash = str_hash.substr(1).toLowerCase();
        var params = str_hash.split('&');
        var res = {};
        for(var param of params)
        {
            param = param.split('=');
            if(param.length !==  2) continue;
            var val = param[1].trim();
            if(val === '') continue;
            res[param[0]] = param[1];
        }
        return res;
    }

    $.fn.url_fragment = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.url_fragment' );
        }
    };

    //helper functions

// End of closure.
}( jQuery));