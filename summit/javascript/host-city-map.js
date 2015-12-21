// City nav active on scroll

// Setup the different icons and shadows
var iconURLPrefix = '//openstack.org/summit/images/mapicons/';

var icons = [
    iconURLPrefix + 'venue.png',
    iconURLPrefix + 'airport.png',
    iconURLPrefix + '1.png',
    iconURLPrefix + '2.png',
    iconURLPrefix + '3.png',
    iconURLPrefix + '4.png',
    iconURLPrefix + '5.png',
    iconURLPrefix + '6.png',
    iconURLPrefix + '7.png',
    iconURLPrefix + '8.png',
    iconURLPrefix + '9.png',
    iconURLPrefix + '10.png',
    iconURLPrefix + '11.png',
    iconURLPrefix + '12.png',
    iconURLPrefix + '13.png',
    iconURLPrefix + '14.png',
    iconURLPrefix + '15.png',
    iconURLPrefix + '16.png',
    iconURLPrefix + '17.png',
    iconURLPrefix + '18.png',
    iconURLPrefix + '19.png',
    iconURLPrefix + '20.png'
];

var shadow = {
    anchor: new google.maps.Point(15, 33),
    url: iconURLPrefix + 'shadow50.png'
};

var map = null;

var infowindow = new google.maps.InfoWindow({
    maxWidth: 400
});

var marker;
var markers = new Array();
var location_markers = [];
var iconCounter = 0;

$(document).ready(function () {

    map = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom: 16,
        scrollwheel: false,
        center: new google.maps.LatLng(settings.host_city_lat, settings.host_city_lng),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: false,
        streetViewControl: false,
        panControl: false,
        zoomControlOptions: {
            position: google.maps.ControlPosition.LEFT_BOTTOM
        }
    });

    $('.marker-link').click(function (event) {
        //event.preventDefault();
        // The function to trigger the marker click, 'id' is the reference index to the 'markers' array.
        var id = parseInt($(this).attr('data-location-id'));
        var marker_pos = location_markers[id];
        console.log(marker_pos);
        google.maps.event.trigger(markers[marker_pos], 'click');
        //return false;
    });

    // Add the markers and infowindows to the map
    for (var i = 0; i < locations.length; i++) {
        var location = locations[i];
        if(location.type =='SummitAirport'){
            iconCounter = 1;
        }
        else if(location.type == 'SummitVenue'){
            iconCounter = 0;
        }
        else {
            var min = 2;
            var max = icons.length;
            iconCounter = Math.floor(Math.random() * (max - min + 1)) + min
        }

        marker = new google.maps.Marker({
            position: new google.maps.LatLng(location.lat, location.lng),
            map: map,
            icon : icons[iconCounter],
            shadow: shadow
        });

        location_markers[location.id] = i;
        markers.push(marker);

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infowindow.setContent(location.name+' '+location.description+' '+location.address);
                infowindow.open(map, marker);
            }
        })(marker, i));

    }

});


// AutoCenter();

function AutoCenter() {
    //  Create a new viewpoint bound
    var bounds = new google.maps.LatLngBounds();
    //  Go through each...
    $.each(markers, function (index, marker) {
        bounds.extend(marker.position);
    });
    //  Fit these bounds to the map
    map.fitBounds(bounds);
}