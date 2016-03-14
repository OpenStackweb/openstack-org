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

var map;
var bounds;
var markers = [];

$(document).ready(function(){
    initMap();

    $('.header').on('click',function(){
        var venue_id = $(this).attr('id');
        clickVenue(venue_id);
    });

    handleDeepLink();

});

function initMap() {
    var infowindow = new google.maps.InfoWindow();

    bounds = new google.maps.LatLngBounds();
    map = new google.maps.Map(document.getElementById('map'), {
        scrollwheel: false,
        zoom: 4
    });

    for (var i in coordinates) {
        var latlng = coordinates[i];
        var title = latlng.title;
        var id = latlng.id;
        delete latlng.title;
        delete latlng.id;

        // Create a marker and set its position.
        var marker = new google.maps.Marker({
            map: map,
            position: latlng,
            title: title,
            id: id
        });

        markers[id] = marker;

        marker.addListener('click', function() {
            clickVenue(marker.id);
        });

        bounds.extend(marker.position);
    }

    map.fitBounds(bounds);

}

function clickVenue(venue_id) {
    var elem = $('#'+venue_id);
    var opened_elem = $('.opened');
    var is_opened = elem.hasClass('opened');

    opened_elem.siblings('.carousel').slideUp();

    opened_elem.animate({
        height: "350"
    }, 500, function() {
        // Animation complete.
    });

    $('.image',opened_elem).fadeIn();
    opened_elem.removeClass('opened');

    if (is_opened) {
        map.fitBounds(bounds);
    } else {
        elem.animate({
            height: "200"
        }, 500, function() {
            // Animation complete.
        });

        $('.image',elem).fadeOut();
        elem.siblings('.carousel').slideDown();
        elem.addClass('opened');

        map.setCenter(markers[venue_id].getPosition());
        map.setZoom(17);
    }
}

function handleDeepLink() {
    var hash = $(window).url_fragment('getParams');
    if(!$.isEmptyObject(hash) && ('venue' in hash) && hash['venue'] ) {
        var venue_id = hash['venue'];
        if ($('#'+venue_id).length) {
            $('body').delay(1000).animate({scrollTop: $('#'+venue_id).offset().top }, 2000, function(){
                clickVenue(venue_id);
            });
        }
    }
}