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

    $.validator.addMethod("ValidNewCompanyName", function (value, element, arg) {
        value = value.trim();
        var field = arg[0];
        if(value == '0' && field.val().trim() === '')
            return false;
        return true;
    }, "You Must Specify a New Company Name!.");

    //private methods
    var methods = {
        setCompany: function(company){
            var ddl   = $('select.select-company-name', control);
            var input = $('input.input-company-name',  control);
            if(company.id > 0){
                ddl.val(company.id);
            }
            else{
                input.val(company.name);
                ddl.val(0);
                input.show();
            }
        },
        getCompany: function(){

            var ddl          = $('select.select-company-name', control);
            var input        = $('input.input-company-name',  control);
            var company_id   = ddl.val() > 0 ? $("option:selected", ddl).val() : input.attr('data-company-id');
            var company_name = ddl.val() > 0 ? $("option:selected", ddl).text(): input.val();

            return {'id': company_id, 'name': company_name};
        },
        init: function(options){

            settings = $.extend(settings , options);

            control = $(this);

            form      = $('form.input-form');
            var ddl   = $('select.select-company-name', control);
            var input = $('input.input-company-name',  control);

            if(input.length > 0) {
                input.autocomplete({
                    source: 'api/v1/job-registration-requests/companies',
                    minLength: 2,
                    select: function( event, ui ) {
                        var company_id = 0;
                        if(ui.item){
                            company_id = ui.item.id;
                        }
                        input.attr('data-company-id', company_id);
                    }
                })
            }

            if(ddl.length > 0) {

                input.hide();
                input.val( $('option:selected', ddl).text() );

                ddl.rules('add',{
                    required:true,
                    ValidNewCompanyName:[input]
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

    $.fn.company_field = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.company_field' );
        }
    };

    //helper functions

// End of closure.
}( jQuery));