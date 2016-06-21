// City nav active on scroll

// Setup the different icons and shadows
var iconURLPrefix = '/summit/images/mapicons/';

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

var marker           = null;
var markers          = new Array();
var location_markers = [];
var iconCounter      = 2;

$(document).ready(function () {

    if(settings.host_city_lat == '' || settings.host_city_lng == '' && locations.length > 0)
    {
        var first_location = locations[0];
        settings.host_city_lat =  first_location.lat;
        settings.host_city_lng =  first_location.lng;
    }

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
        // The function to trigger the marker click, 'id' is the reference index to the 'markers' array.
        var id = parseInt($(this).attr('data-location-id'));
        var marker_pos = location_markers[id];
        var marker = markers[marker_pos]
        google.maps.event.trigger(marker, 'click');
    });

    // Add the markers and infowindows to the map
    for (var i = 0; i < locations.length; i++) {
        var location = locations[i];
        var icon = null;
        if (location.type =='SummitExternalLocation'){
            continue;
        }
        else if(location.type =='SummitAirport'){
            icon =  icons[1];
        }
        else if(location.type == 'SummitVenue'){
            icon = icons[0];
        }
        else
        {
            iconCounter = iconCounter+1 > icons.length ? 2 : iconCounter;
            icon = icons[iconCounter];
            iconCounter++;
        }

        //console.log(location.name+' lat '+location.lat+' lng '+location.lng);
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(location.lat, location.lng),
            map: map,
            icon : icon,
            shadow: shadow,
            pos: i
        });

        location_markers[location.id] = i;
        markers.push(marker);

        google.maps.event.addListener(marker, 'click', function() {
            var marker_pos = this.get('pos');
            var location   = locations[marker_pos];
            infowindow.setContent(location.name+' '+location.description+' '+location.address);
            infowindow.open(map, this);
        });
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