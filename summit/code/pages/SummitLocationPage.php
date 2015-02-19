<?php

class SummitLocationPage extends SummitPage {
    private static $db = array (
        'VisaInformation' => 'HTMLText'
    );
    
	private static $has_many = array (
		'Locations' => 'SummitLocation'
	);    
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
                
        $fields->addFieldToTab('Root.Main', new HTMLEditorField('VisaInformation'), 'Content');
        
        if($this->ID) {
                        
            // Summit Question Categories
            $LocationFields = singleton('SummitLocation')->getCMSFields();
            $config = GridFieldConfig_RelationEditor::create();
            $config->getComponentByType('GridFieldDetailForm')->setFields($LocationFields);
            $config->addComponent(new GridFieldSortableRows('Order'));            
            $gridField = new GridField('Locations', 'Locations', $this->Locations(), $config);
            $fields->addFieldToTab('Root.MapLocations',$gridField);        
            
        }
        return $fields;    

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
    
    public function Hotels() {
        return $this->Locations()->filter(array('Type' => 'Hotel'))->sort('Order');
    }
    
    public function Airport() {
        return $this->Locations()->filter(array('Type' => 'Airport'))->sort('Order')->first();
    }

    public function Venue() {
        return $this->Locations()->filter(array('Type' => 'Venue'))->sort('Order')->first();
    }

    
    public function MapScript() {
        
        $MapScript = "
        
           // Google Maps
            // Define locations: HTML content for the info window, latitude, longitude
                var locations = [
        
        ";
        
        // Loop Through All The Locations and add them to the array
        
        $Locations = $this->Locations()->sort('Order');
        
        foreach ($Locations as $Location) {
            
            if($Location->BookingLink) {
                $Link = $Location->BookingLink;
            } else {
                $Link = $Location->Website;
            }
            
            if($Location->IsSoldOut) {
                $BookingBlock = '<p class="sold-out-hotel">SOLD OUT</p>';
            } else {
                $BookingBlock = '<br><a href=\"".$Link."\" target=\"_blank\" alt=\"Visit Website\">Visit Website</a></p>';
            }
            
            $MapScript = $MapScript . "
            
                ['<h5>".$Location->Name."<span>".$Location->Description."</span></h5><p>".$Location->Address.$BookingBlock."', ".$Location->Latitude.", ".$Location->Longitude."],
            
            ";
            
        }
    
        $MapScript = $MapScript .
           "

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
        
        return $MapScript;
    
    }

	
}