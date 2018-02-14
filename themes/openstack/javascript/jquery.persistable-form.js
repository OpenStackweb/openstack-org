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

    var form        = null;
    var settings    = {};
    var storageKey  = 'new';
    var localObj    = {};

    // localStorage feature detect
    function supportsLocalStorage() {
        return typeof(Storage)!== 'undefined';
    }

    function isInArray(value, array) {
        return array.indexOf(value) > -1;
    }

    function initLocalObj() {
        var entityIdVal = $('#'+settings.entityId, form).val();
        storageKey = entityIdVal ? entityIdVal : 'new';

        var retrievedObject = localStorage.getItem(storageKey);
        localObj = (retrievedObject) ? JSON.parse(retrievedObject) : {};
    }

    function storeLocalObj() {
        localStorage.setItem(storageKey, JSON.stringify(localObj));
    }

    function getLocalItem(id) {
        if(!isInArray(id, settings.ignoreFields) && localObj) {
            return (localObj.hasOwnProperty(id) ? localObj[id] : null);
        } else {
            return null;
        }
    }

    function setLocalItem(id, value) {
        localObj[id] = value;
    }

    function _innerReload(){

        var selects   = $('select', form);
        var inputs    = $('input', form);
        var textareas = $('textarea', form);

        selects.each(function () {
            var select = $(this);
            var id     = select.prop("id");
            var val = getLocalItem(id);

            if(val != null && val != '')
                select.val(val).change();
        });

        inputs.each(function () {
            var input = $(this);
            var id    = input.prop("id");
            var val = getLocalItem(id);

            if(val == null) return;
            if(input.is(':radio')){
                input.prop('checked', (val === true)).change();
            }
            else if(input.is(':checkbox')){
                input.prop('checked', (val === true)).change();
            }
            else{
                input.val(val);
            }
        });

        textareas.each(function () {
            var textarea = $(this);
            var id       = textarea.prop("id");
            val = getLocalItem(id);

            if(val == null || val == '') return;
            textarea.val(val);
        });
    }

    var methods = {
        init : function(options) {
            form = $(this);

            // Establish our default settings
            settings = $.extend({
                entityId: '',
                ignoreFields : []
            }, options);

            if(!supportsLocalStorage()){
                console.log('HTML5 local storage not supported!');
                return;
            }

            if(!settings.entityId){
                console.log('Need to set the entity id.');
                return;
            }

            initLocalObj();

            window.setInterval(function(){
                var selects   = $('select', form);
                var inputs    = $('input', form);
                var textareas = $('textarea', form);

                try {
                    selects.each(function () {
                        var select = $(this);
                        var id     = select.prop("id");

                        setLocalItem(id, select.val());
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
                            setLocalItem(id, val);
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

                        setLocalItem(id, val);
                    });

                    storeLocalObj();
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
                var val   = getLocalItem(id);

                if(val == null) return;
                if(input.is(':radio')){
                   input.prop('checked', (val === true));
                }
                else if(input.is(':checkbox')){
                   input.prop('checked', (val === true));
                }
                else{
                   input.val(val);
                }

                // trigger change only when val is true
                if (val === true) {
                    input.change();
                }

            }
        },
        clearAll: function(){

            if(!supportsLocalStorage()){
                console.log('HTML5 local storage not supported!');
                return;
            }
            console.log('clearing local storage ...');
            localStorage.clear();
        },
        clearOne: function(id){

            if(!supportsLocalStorage()){
                console.log('HTML5 local storage not supported!');
                return;
            }
            console.log('clearing local storage for '+id);
            localStorage.removeItem(id);
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