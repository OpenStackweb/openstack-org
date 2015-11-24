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
var num = 980; //number of pixels before modifying styles

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
        ['<h5>Austin Convention Center</h5><p>500 East Cesar Chavez Street<br>Austin, TX 78701</p>', 30.263650, -97.739600],
        ['<h5>W Austin</h5><p>200 Lavaca St<br>Austin, TX 78701</p>', 30.265848, -97.747067],
        ['<h5>JW Marriot Austin</h5><p>110 E 2nd St<br> Austin, TX 78701</p>', 30.264765, -97.743414],
        ['<h5>Radisson Hotel & Suites Austin Downtown</h5><p>111 E Cesar Chavez St<br>Austin, TX 78701</p>', 30.262946, -97.743992],
        ['<h5>Hyatt Place Austin Downtown</h5><p>211 E 3rd St<br>Austin, TX 78701</p>', 30.264694, -97.741828],
        ['<h5>Hampton Inn & Suites Austin-Downtown/Convention Center</h5><p>200 San Jacinto Blvd<br>Austin, TX 78701</p>', 30.264215, -97.741946],
        ['<h5>Four Seasons Hotel Austin</h5><p>98 San Jacinto Blvd<br>Austin, TX 78701</p>', 30.262052, -97.742411],
        ['<h5>Hotel Van Zandt, a Kimpton Hotel</h5><p>605 Davis St<br>Austin, TX 78701</p>', 30.260318, -97.738842],
        ['<h5>The Westin Austin Downtown</h5><p>310 E 5th St<br>Austin, TX 78701</p>', 30.266814, -97.740512],
        ['<h5>Residence Inn Austin Downtown/Convention Center</h5><p>300 E 4th St<br>Austin, TX 78701</p>', 30.265670, -97.740487],
        ['<h5>Hilton Austin</h5><p>500 E 4th St<br>Austin, TX 78701</p>', 30.265916, -97.738287],
        ['<h5>Hilton Garden Inn Austin Downtown Convention Center</h5><p>N 500 Interstate 35 Frontage Rd<br>Austin, TX 78701</p>', 30.265240, -97.735734],
        ['<h5>Austin-Bergstrom International Airport</h5><p>3600 Presidential Blvd, Austin, TX 78719</p>', 30.197550, -97.666284],
    ];
    
    // Setup the different icons and shadows
    var iconURLPrefix = '//iamweswilson.com/img/mapicons/';
    
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
      iconURLPrefix + '9.png',
      iconURLPrefix + '10.png',
      iconURLPrefix + '11.png',
      iconURLPrefix + 'airport.png',
    ]
    var icons_length = icons.length;
    
    
    var shadow = {
      anchor: new google.maps.Point(15,33),
      url: iconURLPrefix + 'shadow50.png'
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'), {
      zoom: 16,
      scrollwheel: false,
      center: new google.maps.LatLng(30.263650, -97.739600),
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