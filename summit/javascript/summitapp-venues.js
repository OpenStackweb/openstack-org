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

var infowindow = new google.maps.InfoWindow({
    maxWidth: 400
});


$(document).ready(function(){
    initMap();

    $('.header').on('click',function(){
        var venue_id = $(this).attr('id');
        clickVenue(venue_id,0);
    });

    $('.floor_header').on('click',function(){
        $('.floor_image').slideUp();
        $(this).siblings('.floor_image').slideDown();

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

    for (var i in primary_locations) {
        var location = primary_locations[i];

        // Create a marker and set its position.
        var marker = new google.maps.Marker({
            map: map,
            icon : '/summit/images/mapicons/venue.png',
            position: new google.maps.LatLng(location.lat, location.lng),
            title: location.title,
            description: location.description,
            address: location.address,
            id: location.id
        });

        markers[location.id] = marker;

        google.maps.event.addListener(marker, 'click', function() {
            clickVenue(this.id,0);
        });

        bounds.extend(marker.position);
    }

    map.fitBounds(bounds);

}

function clickVenue(venue_id, floor_id) {
    var elem = $('#'+venue_id);
    var marker = markers[venue_id];
    var opened_elem = $('.opened');
    var is_opened = elem.hasClass('opened');

    infowindow.setContent(marker.title+' '+marker.description+' '+marker.address);
    infowindow.open(map, marker);

    opened_elem.siblings('.carousel').slideUp();
    opened_elem.siblings('.floor-accordion').slideUp();

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
        elem.siblings('.floor-accordion').slideDown();
        if (floor_id) {
            $('#floor_'+floor_id).slideDown();
        }
        elem.addClass('opened');

        map.setCenter(marker.getPosition());
        map.setZoom(17);
    }
}

function handleDeepLink() {
    var hash = $(window).url_fragment('getParams');
    if(!$.isEmptyObject(hash) && ('venue' in hash) && hash['venue'] ) {
        var venue_id = hash['venue'];
        if ($('#'+venue_id).length) {
            $('body').delay(1000).animate({scrollTop: $('#'+venue_id).offset().top }, 2000, function(){
                clickVenue(venue_id,0);
            });
        }
    }

    if(!$.isEmptyObject(hash) && ('room' in hash) && hash['room'] ) {
        var room_id = hash['room'];
        var room = rooms[room_id];
        if ($('#'+room.venue_id).length) {
            $('body').delay(1000).animate({scrollTop: $('#'+room.venue_id).offset().top }, 2000, function(){
                clickVenue(room.venue_id,room.floor_id);
            });
        }
    }
}