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

    for(var code in countries_with_deployment){
        var country        = countries_with_deployment[code];
        var country_data   = countries_data[code];
        if(country != undefined && country_data != undefined) {
            var country_color = Math.floor(Math.random()*16777215).toString(16);
            country_data.label = country.name + ' (' + country.deployments + ')';
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

    $('#range').change(function(event){
        var range = $(this).val();
        $('#survey_range').val(range);
        $("#range_form").submit();
    });

});
