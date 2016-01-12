/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
(function( $ ){

    var element = null;
    var text    = 'loading ...';
    var methods = {
        init : function(options) {
            element = $(this);

            if(element.find('#resultLoading').attr('id') != 'resultLoading'){
                element.append('<div id="resultLoading" style="display:none"><div><img src="themes/openstack/javascript/images/loader.gif"><div>'+text+'</div></div><div class="bg"></div></div>');
            }

            jQuery('#resultLoading').css({
                'width':'100%',
                'height':'100%',
                'position':'fixed',
                'z-index':'10000000',
                'top':'0',
                'left':'0',
                'margin':'auto'
            });

            jQuery('#resultLoading .bg').css({
                'background':'#000000',
                'opacity':'0.7',
                'width':'100%',
                'height':'100%',
                'position':'absolute',
                'top':'0'
            });

            jQuery('#resultLoading>div:first').css({
                'width': '250px',
                'height':'75px',
                'text-align': 'center',
                'position': 'fixed',
                'top':'0',
                'left':'0',
                'right':'0',
                'bottom':'0',
                'margin':'auto',
                'font-size':'16px',
                'z-index':'10',
                'color':'#ffffff'

            });

            jQuery('#resultLoading .bg').height('100%');
            jQuery('#resultLoading').fadeIn(500);
            element.css('cursor', 'wait');
        },
        stop: function(){
            $('#resultLoading .bg').height('100%');
            $('#resultLoading').fadeOut(1000);
            element.css('cursor', 'default');
        }
    };

    $.fn.ajax_loader = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.ajax_loader' );
        }
    };

    //helper functions

// End of closure.
}( jQuery));