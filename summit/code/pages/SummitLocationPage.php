<?php

class SummitLocationPage extends SummitPage {
    private static $db = array (
        'VisaInformation' => 'HTMLText'
    );


    public function getCMSFields() {
        $f = parent::getCMSFields();

        return $f
            ->tab('Main')
                ->htmlEditor('VisaInformation')
        ;
    }    
}


class SummitLocationPage_Controller extends SummitPage_Controller {

    public function init() {
        
        $this->top_section = 'full';
        
        parent::init();
                            
        Requirements::javascript('https://maps.googleapis.com/maps/api/js?v=3.exp');
		Requirements::javascript("summit/javascript/host-city.js");
        Requirements::customScript($this->MapScript());
        
	} 
    
    public function MapScript() {
    
        return
           "
           // Google Maps
            // Define locations: HTML content for the info window, latitude, longitude
                var locations = [
                        ['<h5>Vancouver Convention Centre</h5><p>1055 Canada Pl, Vancouver, BC<br>V6C 0C3, Canada</p>', 49.289431, -123.116381],
                        ['<h5>Pan Pacific Vancouver Hotel</h5><p>300-999 Canada Place Way | Suite 300<br>Vancouver, British Columbia V6C3B5, Canada<br><a href=\"http://www.panpacificvancouver.com\" target=\"_blank\" alt=\"Visit Website\">Visit Website</a></p>', 49.288137, -123.113232],
                        ['<h5>Fairmont Waterfront</h5><p>900 Canada Place Way<br>Vancouver, British Columbia V6C 3L5, Canada<br></p><p class=\"sold-out-hotel\">SOLD OUT</p>', 49.287546, -123.113393],
                        ['<h5>Fairmont Pacific Rim</h5><p>1038 Canada Place<br>Vancouver, British Columbia V6C 0B9, Canada<br><a href=\"http://www.fairmont.com/pacific-rim-vancouver/\" target=\"_blank\" alt=\"Visit Website\">Visit Website</a></p>', 49.288427, -123.116851],
                        ['<h5>Pinnacle Vancouver Harbourfront Hotel<span>formerly Renaissance Vancouver Harbourside</span></h5><p>1133 West Hastings Street<br>Vancouver, British Columbia V6E3T3, Canada<br><a href=\"http://www.marriott.com/hotels/travel/yvrrd-renaissance-vancouver-harbourside-hotel/\" target=\"_blank\" alt=\"Visit Website\">Visit Website</a></p>', 49.288617, -123.121028],
                        ['<h5>Vancouver Marriott Downtown</h5><p>1128 West Hastings Street<br>Vancouver, British Columbia V6E 4R5, Canada<br><a href=\"http://www.marriott.com/hotels/travel/yvrdt-vancouver-marriott-pinnacle-downtown-hotel/\" target=\"_blank\" alt=\"Visit Website\">Visit Website</a></p>', 49.288186, -123.120250],
                        ['<h5>Fairmont Hotel Vancouver</h5><p>900 West Georgia Street<br>Vancouver, British Columbia V6C 2W6, Canada<br><a href=\"http://www.fairmont.com/hotel-vancouver/\" target=\"_blank\" alt=\"Visit Website\">Visit Website</a></p>', 49.283901, -123.120957],
                        ['<h5>Hyatt Regency Vancouver</h5><p>655 Burrard Street<br>Vancouver, British Columbia V6C 2R7, Canada<br><a href=\"http://vancouver.hyatt.com/en/hotel/home.html\" target=\"_blank\" alt=\"Visit Website\">Visit Website</a></p>', 49.285695, -123.119663],
                        ['<h5>Four Seasons Hotel Vancouver</h5><p>791 West Georgia Street<br>Vancouver, British Columbia V6C 2T4, Canada<br><a href=\"http://www.fourseasons.com/vancouver/\" target=\"_blank\" alt=\"Visit Website\">Visit Website</a></p>', 49.283805, -123.117930],
                        ['<h5>Vancouver International Airport</h5><p>791 West Georgia Street<br>Vancouver, British Columbia V6C 2T4, Canada<br><a href=\"http://www.fourseasons.com/vancouver/\" target=\"_blank\" alt=\"Visit Website\">Visit Website</a></p>', 49.193537, -123.179974]
                    ];
                    
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
                    
";
    
    }

	
}