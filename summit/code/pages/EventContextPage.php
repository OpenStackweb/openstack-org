<?php

/* 
   Used to set a clean, stripped-down template for legal and terms pages
   These pages contain no nav and just a back button
*/

class EventContextPage extends SummitPage {

}


class EventContextPage_Controller extends SummitPage_Controller {

    public function init()
    {
        parent::init();

        Requirements::block('summit/css/combined.css');
        Requirements::css('node_modules/@fortawesome/fontawesome-pro/css/all.css');
        Requirements::css('themes/openstack/css/static.combined.css');
        Requirements::css('summit/css/general-events-landing-page.css');
        Requirements::css('summit/css/static-event-about-page.css');
        Requirements::javascript('summit/javascript/in-view.min.js');
        Requirements::javascript('summit/javascript/static-summit-about-page.js');
    }

}