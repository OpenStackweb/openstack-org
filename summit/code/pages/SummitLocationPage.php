<?php

/**
 * Class SummitLocationPage
 */
class SummitLocationPage extends SummitPage {

    private static $db = array (
        'VisaInformation' => 'HTMLText',
        'CityIntro'      => 'HTMLText',
        'LocationsTextHeader' => 'HTMLText',
        'OtherLocations' => 'HTMLText',
        'GettingAround' => 'HTMLText',
        'AboutTheCity' => 'HTMLText',
        'Locals'       => 'HTMLText',
        'AboutTheCityBackgroundImageHero' => 'Text',
        'AboutTheCityBackgroundImageHeroSource' => 'Text',
        'HostCityLat' => 'Text',
        'HostCityLng' => 'Text',
        'VenueTitleText' => 'Text',
        'AirportsTitle' => 'Text',
        'AirportsSubTitle' => 'Text',
     );

    private static $has_one = array(
        'VenueBackgroundImage' => 'BetterImage',
        'AboutTheCityBackgroundImage' => 'BetterImage'
    );
    
	private static $has_many = array (
		'Locations' => 'SummitLocation'
	);    
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
                
        $fields->addFieldToTab('Root.Main', new HTMLEditorField('VisaInformation', 'Visa Information'));
        $fields->addFieldToTab('Root.CityInfo', new HTMLEditorField('CityIntro','City Intro'));
        $fields->addFieldToTab('Root.CityInfo', new HTMLEditorField('AboutTheCity','About The City'));
        $fields->addFieldToTab('Root.CityInfo', new HTMLEditorField('Locals','In The Words Of The Locals'));
        $fields->addFieldToTab('Root.CityInfo', new HTMLEditorField('GettingAround','Getting Around'));

        $fields->addFieldsToTab('Root.CityInfo',new TextField('HostCityLat', 'City Latitude (Map Center)'));
        $fields->addFieldsToTab('Root.CityInfo',new TextField('HostCityLng', 'City Longitude (Map Center)'));

        $fields->addFieldToTab('Root.MapLocations', new HTMLEditorField('LocationsTextHeader','Intro Text'));
        $fields->addFieldToTab('Root.MapLocations', new HTMLEditorField('OtherLocations','Other Locations'));



        if($this->ID) {
                        
            // Summit Question Categories
            $LocationFields = singleton('SummitLocation')->getCMSFields();
            $config = GridFieldConfig_RelationEditor::create();
            $config->getComponentByType('GridFieldDetailForm')->setFields($LocationFields);
            $config->addComponent(new GridFieldSortableRows('Order'));            
            $gridField = new GridField('Locations', 'Locations', $this->Locations(), $config);
            $fields->addFieldToTab('Root.MapLocations',$gridField);

            $fields->addFieldsToTab('Root.Main', $venue_back = new UploadField('VenueBackgroundImage', 'Venue Background Image'));
            $venue_back->setFolderName('summits/locations');
            $venue_back->setAllowedMaxFileNumber(1);
            $venue_back->setAllowedFileCategories('image');

            $fields->addFieldsToTab('Root.Main',new TextField('VenueBackgroundImageHero', 'Venue Background Image Author'));
            $fields->addFieldsToTab('Root.Main',new TextField('VenueBackgroundImageHeroSource', 'Venue Background Image Author Url'));

            $fields->addFieldsToTab('Root.CityInfo', $about_back = new UploadField('AboutTheCityBackgroundImage', 'About The City Background Image'));
            $about_back->setFolderName('summits/location/about');
            $about_back->setAllowedMaxFileNumber(1);
            $about_back->setAllowedFileCategories('image');

            $fields->addFieldsToTab('Root.CityInfo',new TextField('AboutTheCityBackgroundImageHero', 'About The City Background Image Author'));
            $fields->addFieldsToTab('Root.CityInfo',new TextField('AboutTheCityBackgroundImageHeroSource', 'About The City Background Image Author Source Url'));
        }

        $fields->addFieldToTab('Root.Main', new TextField('VenueTitleText', 'Venue Title Text'));
        $fields->addFieldToTab('Root.Main', new TextField('AirportsTitle', 'Airports Title'));
        $fields->addFieldToTab('Root.Main', new TextField('AirportsSubTitle', 'Airports SubTitle'));

        return $fields;    

    }

    public function getCityIntro(){
        $res = $this->getField('CityIntro');
        if(empty($res) && $this->SummitID == 4)
            $res = '<blockquote>
					<strong>You’re gorgeous, baby, you’re sophisticated, you live well...</strong>
					Vancouver is Manhattan with mountains. It’s a liquid city, a tomorrow city, equal parts India, China, England, France and the Pacific Northwest. It’s the cool North American sibling.
				</blockquote>
				<div class="testimonial-attribute">
					<img src="/summit/images/nytimes.png">
					<p>New York Times on Vancouver</p>
				</div>';
        return $res;
    }

    public function VenueBackgroundImageUrl(){
        if($this->VenueBackgroundImage()->exists()){
            return $this->VenueBackgroundImage()->getURL();
        }
        return '/summit/images/venue-bkgd.jpg';
    }

    public function getVenueTitleText(){
        $text = $this->getField('VenueTitleText');
        if(empty($text)) $text ='The Venue';
        return $text;
    }

    public function getVenueBackgroundImageHero(){
        $res = $this->getField('VenueBackgroundImageHero');
        if(empty($res))
            return 'Photo by Nick Sinclair';
        return $res;
    }

    public function getVenueBackgroundImageHeroSource(){
        $res = $this->getField('VenueBackgroundImageHeroSource');
        if(empty($res))
            return 'https://flic.kr/p/8rYHEd';
        return $res;
    }

    public function getLocationsTextHeader(){
        $res = $this->getField('LocationsTextHeader');
        if(empty($res) && $this->SummitID == 4)
            $res = '<p>We\'ve negotiated discount rates with six hotels adjacent to the Vancouver Convention Centre (Summit
                venue). Please move quickly to reserve a room before they sell out!</p>';
        return $res;
    }

    public function getOtherLocations(){
        $res = $this->getField('OtherLocations');
        if(empty($res) && $this->SummitID == 4)
            $res = ' <p>
                If you plan to bring your family with you to Vancouver or if you would like to have more space than a
                hotel room offers then you may want to rent an apartment or condo during your stay. The following sites
                are recommended for short-term property rentals.
            </p>
                   <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <a target="_blank"
                       href="http://www.vrbo.com/vacation-rentals/canada/british-columbia/vancouver-area/vancouver?from-date=2015-05-18&to-date=2015-05-22&datesfirm=">VRBO</a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <a target="_blank"
                       href="https://www.airbnb.com/s/Vancouver--BC--Canada?checkin=05%2F18%2F2015&checkout=05%2F22%2F2015&ss_id=xwszyia1">Airbnb</a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <a target="_blank"
                       href="http://www.homeaway.com/search/british-columbia/vancouver/region:6437/arrival:2015-05-18/departure:2015-05-22">HomeAway</a>
                </div>
            </div>
            ';
        return $res;
    }

    public function getGettingAround(){
        $res = $this->getField('GettingAround');
        if(empty($res) && $this->SummitID == 4)
            $res=  '  <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h1>Getting Around In Vancouver</h1>

                <p>
                    There are several safe and reliable transportation options in Vancouver. Here are a few options to
                    consider to get you from Vancouver International Airport to The Vancouver Convention Centre.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="getting-options">
                    <div class="getting-around-item">
                        <a href="http://www.translink.ca" target="_blank"><i class="fa fa-bus"></i>Translink<span>(public transit)</span></a>
                    </div>
                    <div class="getting-around-item">
                        <a href="http://www.yvr.ca/en/getting-to-from-yvr/taxis.aspx" target="_blank"><i
                                class="fa fa-cab"></i>Taxi</a>
                    </div>
                    <div class="getting-around-item">
                        <a href="http://www.yvr.ca/en/getting-to-from-yvr/courtesy-shuttles.aspx" target="_blank"><i
                                class="fa fa-plane"></i>Shuttles<span>(to specific hotels)</span></a>
                    </div>
                    <div class="getting-around-item">
                        <a href="http://www.yvr.ca/en/getting-to-from-yvr/car-rentals.aspx" target="_blank"><i
                                class="fa fa-car"></i>Rental Car</a>
                    </div>
                    <div class="getting-around-item">
                        <a href="http://www.zipcar.ca/how?zipfleet_id=40436214" target="_blank"><i
                                class="fa zipcar-icon">Z</i>zipcar<span>(car sharing)</span></a>
                    </div>
                </div>
            </div>
        </div>';
        return $res;
    }

    public function getAboutTheCity(){
        $res = $this->getField('AboutTheCity');
        if(empty($res) && $this->SummitID == 4)
            $res = '<p>Mountains, ocean, culture, nightlife all rolled into one beautiful city...</p><h1>Thank you Vancouver!</h1>';
        return $res;
    }

    public function getLocals(){
        $res = $this->getField('Locals');
        if(empty($res) && $this->SummitID == 4)
            $res = '<div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h1>In The Words Of The Locals</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-lg-push-2 col-md-8 col-md-push-2 col-sm-8 col-sm-push-2 local-block">
                <blockquote>
                    Vancouver is addictive.<br/>I came to Vancouver for a 2 week vacation over 15 years ago and never
                    left.
                </blockquote>
                <img class="testimonial-author-img" src="/summit/images/DianeMueller.jpeg">

                <div class="testimonial-attribute">
                    <div class="testimonial-name">Diane Mueller</div>
                    <div class="testimonial-title">Community Development, Red Hat</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 local-place-block">
                <img src="/summit/images/therailwayclub.jpg">

                <h3>Railway Club</h3>

                <p>
                    For my money, the coolest place in town was hip before hipster was a word&mdash;The Railway Club,
                    where KD Lang got her start. Check out the latest indie upstarts, while drinking local brews with
                    their famous Rubin sandwich or my fav the espresso martini and watch the model railway cars run
                    around the ceiling.
                </p>

                <p>
                    <a href="http://therailwayclub.com">therailwayclub.com</a>
                </p>
            </div>
            <div class="col-md-4 local-place-block">
                <img src="/summit/images/MOA-UBC.jpeg">

                <h3>Museum of Anthropology</h3>

                <p>
                    If you are in Vancouver, you are on traditional territories of Musqueam First Nations, and any visit
                    should take into consideration the heritage and history that spans thousands of years and continues
                    to inform and enrich life here in Vancouver. The Museum of Anthropology at UBC does an amazing job
                    of showcasing the rich art &amp; living culture of the First Nations and for my money it’s the one
                    thing you must not leave town without experiencing.
                </p>

                <p>
                    <a href="http://moa.ubc.ca">moa.ubc.ca</a>
                </p>
            </div>
            <div class="col-md-4 local-place-block">
                <img src="/summit/images/gibsons.jpg">

                <h3>Sunshine Coast</h3>

                <p>
                    If you really want to “see” BC, you’ll need to hop on a ferry and come to my stomping grounds on the
                    Sunshine Coast. First stop: hit up the local Beer Farm in Gibsons. Yep, we Canadians grow our own
                    beer too. Persphone’s Brewery has a “Wee Heavy” Stout that cannot be missed!
                </p>

                <p>
                    <a href="http://bigpacific.com">bigpacific.com</a>
                </p>
            </div>
        </div>';
        return $res;
    }

    public function getAboutTheCityBackgroundImageUrl(){
        if($this->AboutTheCityBackgroundImage()->exists()){
            return $this->AboutTheCityBackgroundImage()->getURL();
        }
        return '/summit/images/vancouver-bkgd-orange.jpg';
    }

    public function getAboutTheCityBackgroundImageHero(){
        $res = $this->getField('AboutTheCityBackgroundImageHero');
        if(empty($res))
        return 'Photo by Magnus Larsson';
        return $res;
    }

    public function getAboutTheCityBackgroundImageHeroSource(){
        $res = $this->getField('AboutTheCityBackgroundImageHeroSource');
        if(empty($res))
        return 'https://flic.kr/p/adaKoH';
        return $res;
    }

}


class SummitLocationPage_Controller extends SummitPage_Controller {

    public function init() {
        
        $this->top_section = 'full';

        $lat = $this->HostCityLat;
        if(empty($lat)) $lat = '49.287141';
        $lng= $this->HostCityLng;
        if(empty($lng)) $lng = '-123.116976';
        Requirements::customScript("
        var settings = {
             host_city_lat: {$lat},
             host_city_lng: {$lng}
        };
        ");

        parent::init();
                            
        Requirements::javascript('https://maps.googleapis.com/maps/api/js?v=3.exp');

		Requirements::javascript("summit/javascript/host-city.js");
        Requirements::customScript($this->MapScript());
        
	}
    
    public function Hotels() {
        $getVars = $this->request->getVars();
        if(isset($getVars['showHidden'])) {
            $hotels = $this->Locations()->filter(array('Type' => 'Hotel'))->sort('Order');
        } else {
            $hotels = $this->Locations()->filter(array('Type' => 'Hotel','DisplayOnSite' => TRUE))->sort('Order');
        }
        return $hotels;
    }
    
    public function Airports() {
        return $this->Locations()->filter(array('Type' => 'Airport'))->sort('Order');
    }

    public function Venue() {
        return $this->Locations()->filter(array('Type' => 'Venue'))->sort('Order')->first();
    }
    
    public function MapScript() {
        
        $MapScript = "
            // Google Maps
            // Define locations: HTML content for the info window, latitude, longitude
            var locations = [";
        
        // Loop Through All The Locations and add them to the array

        $getVars = $this->request->getVars();
        if(isset($getVars['showHidden'])) {
            $Locations = $this->Locations()->sort('Order');
        } else {
            $Locations = $this->Locations()->filter('DisplayOnSite',TRUE)->sort('Order');
        }
        
        foreach ($Locations as $Location) {
            
            if($Location->BookingLink) {
                $Link = $Location->BookingLink;
            } else {
                $Link = $Location->Website;
            }
            
            if($Location->IsSoldOut) {
                $BookingBlock = '<p class="sold-out-hotel">SOLD OUT</p>';
            } else {
                $BookingBlock = "<br><a href=\"{$Link}\" target=\"_blank\" alt=\"Visit Website\">Visit Website</a></p>";
            }

            $lat = empty($Location->Latitude)? 0 : $Location->Latitude;
            $lng = empty($Location->Longitude)? 0 : $Location->Longitude;
            $description = empty($Location->Description)? "" : "<span>".$Location->Description."</span>";
            $MapScript = $MapScript . "['<h5>".$Location->Name."</h5>".$description."<p>". str_replace('\r\n','',$Location->Address).$BookingBlock."', ".$lat.", ".$lng.",".$Location->ID.",'".$Location->Type."'],";
        }
        $MapScript = rtrim($MapScript, ',');
    
        $MapScript = $MapScript .
           "];";
        
        return $MapScript;
    
    }

	
}