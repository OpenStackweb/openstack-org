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
        Requirements::css('themes/openstack/bower_assets/jquery-loading/dist/jquery.loading.min.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css("summit/css/summitapp-venues.css");
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('https://maps.googleapis.com/maps/api/js?v=3.exp');
        Requirements::javascript('summit/javascript/summitapp-venues.js');
    }

}