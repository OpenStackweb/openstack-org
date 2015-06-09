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

    // rank widget
    var form       = null;
    var control    = null;
    // This is the easiest way to have default options.
    var settings = {
        // These are the defaults.

    };

    $.validator.addMethod("ValidNewOrgName", function (value, element, arg) {
        value = value.trim();
        var field = arg[0];
        if(value == '0' && field.val().trim() === '')
            return false;
        return true;
    }, "You Must Specify a New Organization Name!.");


    //private methods
    var methods = {

        init: function(options){

            settings = $.extend(settings , options);

            control = $(this);

            form      = $('.survey_step_form');
            var ddl   = $('select.select-organization-name', control);
            var input = $('input.input-organization-name',  control);

            if(input.length > 0) {
                input.autocomplete('/join/register/results', {
                    minChars: 3,
                    selectFirst: true,
                    autoFill: true
                });
            }

            if(ddl.length > 0) {

                input.hide();
                input.val( $('option:selected', ddl).text() );

                ddl.rules('add',{
                    required:true,
                    ValidNewOrgName:[input]
                });

                ddl.change(function(event){
                    var ddl = $(this);

                    if(ddl.val() == '0'){
                        input.show();
                        input.val('');
                    }
                    else{
                        input.hide();
                        input.val( $('option:selected', ddl).text() );
                    }

                });
            }
            else if(input.length > 0) {
                input.rules('add',{required:true});
            }

        }
    };

    $.fn.survey_organization_field = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.survey_organization_field' );
        }
    };

    //helper functions

// End of closure.
}( jQuery));