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

            if ($('.interop-program-version', form).length > 0) {
                $('.interop-program-version', form).rules('add', { required: function (element) {
                    if($('#compatible_compute', form).is(':checked') || $('#compatible_storage', form).is(':checked'))
                        return true;
                    return false;
                }});

                $('.interop-program-version', form).change(function (evt){
                    var state = $(this).attr('checked');
                    $('.interop-program-version:checked', form).prop('checked',false);
                    $(this).prop('checked', state);
                });
            }
        },
        getFormValidator:function(){
            return form_validator;
        },
        load: function(implementation) {
            $('#compatible_compute',form).prop('checked',implementation.compatible_compute);
            $('#compatible_storage',form).prop('checked',implementation.compatible_storage);
            $('#compatible_federated_identity',form).prop('checked',implementation.compatible_federated_identity);

            if(implementation.interop_program_version_id > 0)
                $('#interop_program_version_' + implementation.interop_program_version_id, form).prop('checked', true);

        },
        serialize: function(implementation) {
            var is_valid = form.valid();
            if(!is_valid){
                return false;
            }
            implementation.compatible_compute            = $('#compatible_compute', form).is(':checked');
            implementation.compatible_storage            = $('#compatible_storage', form).is(':checked');
            implementation.compatible_federated_identity = $('#compatible_federated_identity', form).is(':checked');
            var version = $('.interop-program-version:checked', form);

            implementation.interop_program_version_id    = version.length > 0? version.attr('data-version-id'):0;
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
