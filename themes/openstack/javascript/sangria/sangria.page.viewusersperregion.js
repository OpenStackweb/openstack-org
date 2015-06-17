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
jQuery(document).ready(function($){

    //init map widget

    var places = [];

    for(var code in countries_with_users){
        var country        = countries_with_users[code];
        var country_data   = countries_data[code];
        if(country != undefined && country_data != undefined) {
            var country_color = Math.floor(Math.random()*16777215).toString(16);
            country_data.label = country.name + ' (' + country.users + ')';
            country_data.color = country_color;
            places.push(country_data);
        }
    }

    $('#map').google_map({
        places : places,
        getInfo:function(place){
            return '<a href="'+place.url+'"><b>'+place.label+'</b></a>';
        }
    });

    $('.export_cb_all').click(function(){
        var cb_value = $(this).prop('checked');
        $('.export_cb').prop('checked',cb_value);
    });

    $('.export_cb_cont').click(function(){
        var cb_value = $(this).prop('checked');
        $('.export_cb_all').prop('checked',false);
        $('.export_cb',$(this).parent()).prop('checked',cb_value);
    });

    var form_export_members = $('#form-export-members');

    var form_validator = form_export_members.validate({
        onfocusout: false,
        rules: {
            'countries[]'  : {required: true, minlength: 1 },
            'members[]' : {required: true, minlength: 1}
        },
        messages: {
            'countries[]': "Select a country.",
            'members[]': "Select a member type."
        },
        focusInvalid: false,
        invalidHandler: function(form, validator) {
            if (!validator.numberOfInvalids())
                return;
            var element = $(validator.errorList[0].element);
            if(!element.is(":visible")){
                element = element.parent();
            }
        },
        errorPlacement: function(error, element) {
            $('.error').html(error.text()).show();

            //$('#submit_export').after('<span for="countries[]" class="error">'+error.text()+'</span>');
            //error.insertAfter($('#submit_export'));
        }
    });

    form_export_members.submit(function(event){
        var is_valid = form_export_members.valid();
        if(!is_valid){
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    })

});
