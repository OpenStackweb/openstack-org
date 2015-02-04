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
        var deployments    = countries_with_deployment[code];
        var country_data   = countries_data[code];
        if(country_data != undefined && deployments != undefined) {
            for(var i = 0 ; i<= deployments.length - 1 ; i++)
            {
                var deployment = deployments[i];
                places.push({url: deployment.url, label: deployment.name, lat: country_data.lat, lng: country_data.lng  });
            }
        }
    }

    $('#map').google_map({
        places : places,
        getInfo:function(place){
            return '<a href="'+place.url+'"><b>'+place.label+'</b></a>';
        }
    });

    $('.country_link').click(function (e){
        e.stopPropagation();
        var country = $(this).attr('data-country');
        var country_data   = countries_data[code];
        $('#map').google_map('setCenter', country_data.lat, country_data.lng)
        return false;
    });

    $('.country_link:first').trigger('click');

});
