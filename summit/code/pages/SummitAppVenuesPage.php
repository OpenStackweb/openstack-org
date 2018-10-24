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

        GoogleMapScriptBuilder::renderRequirements(null,"3.exp");
        Requirements::javascript('summit/javascript/summitapp-venues.js');
    }

}