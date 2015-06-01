// City nav active on scroll

// City Nav Affix
var num = 1070; //number of pixels before modifying styles

// Setup the different icons and shadows
var iconURLPrefix = 'http://openstack.org/summit/images/mapicons/';

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
]
var icons_length = icons.length;


var shadow = {
    anchor: new google.maps.Point(15,33),
    url: iconURLPrefix + 'shadow50.png'
};

var map = null;

var infowindow = new google.maps.InfoWindow({
    maxWidth: 400
});

var marker;
var markers = new Array();

var iconCounter = 0;
$(document).ready(function () {
    $(document).on("scroll", onScroll);
    
    //smoothscroll
    $('a[href^="#"]').on('click', function (e) {
        e.preventDefault();
        $(document).off("scroll");
        $('a').each(function () {
            $(this).removeClass('active');
        })
        $(this).addClass('active');
      
        var target = this.hash,
            menu = target;
        $target = $(target);
        
        var detla = 0;
        
        // figure out how much room to allow for nav bar
        if ($( '#nav-bar' ).hasClass( 'fixed' )) {
            detla = 60;
        } else {
            detla = 170;
        }
        
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top - detla
        }, 500, 'swing', function () {
            window.location.hash = target;
            $(document).on("scroll", onScroll);
        });
    });

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
});

function onScroll(event){
    var scrollPos = $(document).scrollTop();
    $('.city-nav a').each(function () {
        var currLink = $(this);
        var refElement = $(currLink.attr("href"));
        if (refElement.position().top - 60 <= scrollPos && refElement.position().top + refElement.outerHeight() > scrollPos) {
            $('.city-nav ul li a').removeClass("active");
            currLink.addClass("active");
        }
        else{
            currLink.removeClass("active");
        }
    });
}



$(window).bind('scroll', function () {
    if ($(window).scrollTop() > num) {
        $('.city-nav.city').addClass('fixed');
    } else {
        $('.city-nav.city').removeClass('fixed');
    }
});

    
    // Add the markers and infowindows to the map
    for (var i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map,
        icon : icons[iconCounter],
        shadow: shadow
      });

      markers.push(marker);

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
      
      iconCounter++;
      // We only have a limited number of possible icon colors, so we may have to restart the counter
      if(iconCounter >= icons_length){
        iconCounter = 0;
      }
    }

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
    // AutoCenter();

    // The function to trigger the marker click, 'id' is the reference index to the 'markers' array.
    function myClick(id){
        id = id + 1; // hack that allows for summit venue and airport to be ahaed of hotels
        google.maps.event.trigger(markers[id], 'click');
    }