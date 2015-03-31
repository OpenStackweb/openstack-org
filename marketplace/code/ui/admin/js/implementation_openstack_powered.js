/**
 * Copyright 2015 Openstack Foundation
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
    var form  = null;
    var form_validator = null;
    var methods = {
        init : function(options) {
            form = $(this);

            $('#compute_capabilities', form).rules('add', { required: function (element) {
                return $('#compatible_compute', form).is(':checked');
            }});

            $('#storage_capabilities', form).rules('add', { required: function (element) {
                return $('#compatible_storage', form).is(':checked');
            }});

            $('#platform_capabilities', form).rules('add', { required: function (element) {
                return $('#compatible_platform', form).is(':checked');
            }});

            if((typeof tinyMCE != 'undefined')) {
                tinyMCE.init({
                    theme : "advanced",
                    mode: "exact",
                    elements : "compute_capabilities, storage_capabilities, platform_capabilities",
                    theme_advanced_toolbar_location: "top",
                    theme_advanced_buttons1: "formatselect,|,bold,italic,underline,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,outdent,indent,separator,bullist,link,undo,redo,code",
                    theme_advanced_buttons2 : "",
                    theme_advanced_buttons3 : "",
                    height:"250px",
                    width:"600px"
                });
            }

        },
        getFormValidator:function(){
            return form_validator;
        },
        load: function(implementation) {

            $('#compatible_compute',form).prop('checked',implementation.compatible_compute);
            $('#compatible_storage',form).prop('checked',implementation.compatible_storage);
            $('#compatible_platform',form).prop('checked',implementation.compatible_platform);
            $('#compatible_federated_identity',form).prop('checked',implementation.compatible_federated_identity);

            $('#compute_capabilities', form).val(implementation.compute_capabilities);
            $('#storage_capabilities', form).val(implementation.storage_capabilities);
            $('#platform_capabilities', form).val(implementation.platform_capabilities);

        },

        serialize: function(implementation) {

            implementation.compatible_compute            = $('#compatible_compute', form).is(':checked');
            implementation.compatible_storage            = $('#compatible_storage', form).is(':checked');
            implementation.compatible_platform           = $('#compatible_platform', form).is(':checked');
            implementation.compatible_federated_identity = $('#compatible_federated_identity', form).is(':checked');

            if(implementation.compatible_compute){
                implementation.compute_capabilities = tinymce.get('compute_capabilities').getContent();
            }
            else{
                implementation.compute_capabilities = '';
            }

            if(implementation.compatible_storage){
                implementation.storage_capabilities = tinymce.get('storage_capabilities').getContent();
            }
            else{
                implementation.storage_capabilities = '';
            }

            if(implementation.compatible_platform){
                implementation.platform_capabilities = tinymce.get('platform_capabilities').getContent();
            }
            else{
                implementation.platform_capabilities = '';
            }

            return implementation;
        }
    }

    $.fn.implementation_openstack_powered = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.implementation_openstack_powered' );
        }
    };
    // End of closure.
}( jQuery ));
