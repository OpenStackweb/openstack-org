<?php

/* 
   Static content page for Austin
*/

class SummitStaticAustinGuidePage extends SummitPage {

}


class SummitStaticAustinGuidePage_Controller extends SummitPage_Controller {

    public function init()
    {
        parent::init();
        Requirements::block("summit/css/combined.css");
        Requirements::css("themes/openstack/css/static.combined.css");
        Requirements::javascript("themes/openstack/javascript/guide.js");
    }


}