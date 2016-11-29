/**
 * Copyright 2015 OpenStack Foundation
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

    var form     = null;
    var settings = {};

    // localStorage feature detect
    function supportsLocalStorage() {
        return typeof(Storage)!== 'undefined';
    }

    function isInArray(value, array) {
        return array.indexOf(value) > -1;
    }

    function _innerReload(){

        var selects   = $('select', form);
        var inputs    = $('input', form);
        var textareas = $('textarea', form);

        selects.each(function () {
            var select = $(this);
            var id     = select.prop("id");
            if(isInArray(id, settings.ignoreFields)) return;
            var val = localStorage.getItem(id);
            if(val != null && val != '')
                select.val(val).change();
        });

        inputs.each(function () {
            var input = $(this);
            var id    = input.prop("id");
            if(isInArray(id, settings.ignoreFields)) return;
            var val = localStorage.getItem(id);
            if(val == null) return;
            if(input.is(':radio')){
                input.prop('checked', (val === 'true')).change();
            }
            else if(input.is(':checkbox')){
                input.prop('checked', (val === 'true')).change();
            }
            else{
                input.val(val);
            }
        });

        textareas.each(function () {
            var textarea = $(this);
            var id       = textarea.prop("id");
            if(isInArray(id, settings.ignoreFields)) return;
            val = localStorage.getItem(id);
            if(val == null || val == '') return;
            textarea.val(val);
        });
    }

    var methods = {
        init : function(options) {
            form = $(this);

            // Establish our default settings
            settings = $.extend({
                ignoreFields : []
            }, options);

            if(!supportsLocalStorage()){
                console.log('HTML5 local storage not supported!');
                return;
            }

            window.setInterval(function(){
                var selects   = $('select', form);
                var inputs    = $('input', form);
                var textareas = $('textarea', form);

                try {
                    selects.each(function () {
                        var select = $(this);
                        var id     = select.prop("id");
                        if(isInArray(id, settings.ignoreFields)) return;
                        localStorage.setItem(id, select.val());
                    });

                    inputs.each(function () {
                        var input = $(this);
                        var id    = input.prop("id");
                        if(isInArray(id, settings.ignoreFields)) return;
                        var val = null;
                        if(input.is(':radio')){
                            val = input.is(':checked');
                        }
                        else if(input.is(':checkbox')){
                            val = input.is(':checked');
                        }
                        else{
                            val = input.val();
                        }

                        if(val != null)
                            localStorage.setItem(id, val);
                    });

                    textareas.each(function () {
                        var textarea = $(this);
                        var id       = textarea.prop("id");
                        if(isInArray(id, settings.ignoreFields)) return;
                        var val      = '';
                        if(textarea.hasClass("tinymceeditor")){
                            val = tinyMCE.get(id).getContent();
                        }
                        else
                            val = textarea.val();

                        localStorage.setItem(id, val);
                    });
                }
                catch (e) {

                    // If any errors, catch and alert the user
                    if (e == QUOTA_EXCEEDED_ERR) {
                        alert('Quota exceeded!');
                    }
                }

            }, 2000);

            $(document).ready(function () {
               _innerReload();
            });
        },

        reloadControls: function (controls2Reload) {
            if(!supportsLocalStorage()){
                console.log('HTML5 local storage not supported!');
                return;
            }

            for(var i=0; i < controls2Reload.length; i++){

                var id    = controls2Reload[i];
                var input =  $('#'+id, form);
                var val   = localStorage.getItem(id);

                if(val == null) return;
                if(input.is(':radio')){
                   input.prop('checked', (val === 'true'));
                }
                else if(input.is(':checkbox')){
                   input.prop('checked', (val === 'true'));
                }
                else{
                   input.val(val);
                }

                // trigger change only when val is true
                if (val === 'true') {
                    input.change();
                }

            }
        },
        clearAll: function(){

            if(!supportsLocalStorage()){
                console.log('HTML5 local storage not supported!');
                return;
            }

            localStorage.clear();
        }
    };

    $.fn.persistableForm = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.persistableForm' );
        }
    };

    //helper functions

// End of closure.
}( jQuery));