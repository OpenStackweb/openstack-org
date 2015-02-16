// City nav active on scroll
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

// City Nav Affix
var num = 1070; //number of pixels before modifying styles

$(window).bind('scroll', function () {
    if ($(window).scrollTop() > num) {
        $('.city-nav.city').addClass('fixed');
    } else {
        $('.city-nav.city').removeClass('fixed');
    }
});


// Google Maps
// Define your locations: HTML content for the info window, latitude, longitude
    var locations = [
        ['<h5>Vancouver Convention Centre</h5><p>1055 Canada Pl, Vancouver, BC<br>V6C 0C3, Canada</p>', 49.289431, -123.116381],
        ['<h5>Pan Pacific Vancouver Hotel</h5><p>300-999 Canada Place Way | Suite 300<br>Vancouver, British Columbia V6C3B5, Canada<br><a href="https://resweb.passkey.com/go/OpenStackSummit2015" target="_blank" alt="Book Online">Book Online</a></p>', 49.288137, -123.113232],
        ['<h5>Fairmont Waterfront</h5><p>900 Canada Place Way<br>Vancouver, British Columbia V6C 3L5, Canada<br><a href="https://resweb.passkey.com/go/openstack" target="_blank" alt="Book Online">Book Online</a></p>', 49.287546, -123.113393],
        ['<h5>Fairmont Pacific Rim</h5><p>1038 Canada Place<br>Vancouver, British Columbia V6C 0B9, Canada<br><a href="https://resweb.passkey.com/go/pacificrimopenstack2015" target="_blank" alt="Book Online">Book Online</a></p>', 49.288427, -123.116851],
        ['<h5>Pinnacle Vancouver Harbourfront Hotel<span>formerly Renaissance Vancouver Harbourside</span></h5><p>1133 West Hastings Street<br>Vancouver, British Columbia V6E3T3, Canada<br><a href="https://resweb.passkey.com/go/OpenStackPinnacle" target="_blank" alt="Book Online">Book Online</a></p>', 49.288617, -123.121028],
        ['<h5>Vancouver Marriott Downtown</h5><p>1128 West Hastings Street<br>Vancouver, British Columbia V6E 4R5, Canada<br><a href="https://resweb.passkey.com/go/OpenstackVancouver" target="_blank" alt="Book Online">Book Online</a></p>', 49.288186, -123.120250],
        ['<h5>Fairmont Hotel Vancouver</h5><p>900 West Georgia Street<br>Vancouver, British Columbia V6C 2W6, Canada<br><a href="https://resweb.passkey.com/go/openstackhvc2015" target="_blank" alt="Book Online">Book Online</a></p>', 49.283901, -123.120957],
        ['<h5>Hyatt Regency Vancouver</h5><p>655 Burrard Street<br>Vancouver, British Columbia V6C 2R7, Canada<br><a href="https://resweb.passkey.com/go/openstack2015" target="_blank" alt="Book Online">Book Online</a></p>', 49.285695, -123.119663],
        ['<h5>Four Seasons Hotel Vancouver</h5><p>791 West Georgia Street<br>Vancouver, British Columbia V6C 2T4, Canada<br><a href="http://www.fourseasons.com/vancouver/landing_pages/events/openstack_summit/" target="_blank" alt="Book Online">Book Online</a></p>', 49.283805, -123.117930],
        ['<h5>Vancouver International Airport</h5><p>791 West Georgia Street<br>Vancouver, British Columbia V6C 2T4, Canada<br><a href="http://www.fourseasons.com/vancouver/" target="_blank" alt="Visit Website">Visit Website</a></p>', 49.193537, -123.179974]
    ];
    
    // Setup the different icons and shadows
    var iconURLPrefix = 'http://iamweswilson.com/mapicons/';
    
    var icons = [
      iconURLPrefix + 'venue.png',
      iconURLPrefix + '1.png',
      iconURLPrefix + '2.png',
      iconURLPrefix + '3.png',
      iconURLPrefix + '4.png',
      iconURLPrefix + '5.png',
      iconURLPrefix + '6.png',
      iconURLPrefix + '7.png',
      iconURLPrefix + '8.png',
      iconURLPrefix + 'airport.png'
    ]
    var icons_length = icons.length;
    
    
    var shadow = {
      anchor: new google.maps.Point(15,33),
      url: iconURLPrefix + 'shadow50.png'
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'), {
      zoom: 16,
      scrollwheel: false,
      center: new google.maps.LatLng(49.287141, -123.116976),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: false,
      streetViewControl: false,
      panControl: false,
      zoomControlOptions: {
         position: google.maps.ControlPosition.LEFT_BOTTOM
      }
    });

    var infowindow = new google.maps.InfoWindow({
      maxWidth: 400
    });

    var marker;
    var markers = new Array();
    
    var iconCounter = 0;
    
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
        google.maps.event.trigger(markers[id], 'click');
    }