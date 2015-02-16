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
	}    

	
}