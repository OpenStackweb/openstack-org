<?php

/* 
   Static content page for Austin
*/

class SummitStaticAboutBerlinPage extends SummitPage {

}


class SummitStaticAboutBerlinPage_Controller extends SummitPage_Controller {

    public function init()
    {
        $this->top_section = 'full';
        parent::init();
        Requirements::block('summit/css/combined.css');
        Requirements::css('themes/openstack/static/css/combined.css');
        Requirements::css('themes/openstack/javascript/secondary-nav.jquery/secondary-nav.jquery.css');
        Requirements::javascript('themes/openstack/javascript/secondary-nav.jquery/secondary-nav.jquery.js');
        Requirements::javascript('summit/javascript/summit-about-page.js');
    }

}