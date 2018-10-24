<?php

/**
 * Class SummitAppVenuesPage
 */
class SummitAppVenuesPage extends SummitPage
{

}

/**
 * Class SummitAppVenuesPage_Controller
 */
class SummitAppVenuesPage_Controller extends SummitPage_Controller
{

    static $allowed_actions = array(
    );

    static $url_handlers = array
    (
    );

    public function init()
    {

        $this->top_section = 'short'; //or full
        parent::init();

        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::css("summit/css/summitapp-venues.css");

        $google_map_lib_url = sprintf("https://maps.googleapis.com/maps/api/js?key=%s&v=3.exp", GOOGLE_MAP_KEY);
        Requirements::javascript($google_map_lib_url);
        Requirements::javascript('summit/javascript/summitapp-venues.js');
    }

}