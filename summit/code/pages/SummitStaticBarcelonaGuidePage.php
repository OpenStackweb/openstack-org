<?php

/* 
   Static content page for Barcelona
*/

class SummitStaticBarcelonaGuidePage extends SummitPage {

}


class SummitStaticBarcelonaGuidePage_Controller extends SummitPage_Controller {

    public function init()
    {
        parent::init();
        Requirements::block("summit/css/combined.css");
        Requirements::css("themes/openstack/static/css/combined.css");
        Requirements::javascript("themes/openstack/static/js/guide.js");
    }


}