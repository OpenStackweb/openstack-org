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
        Requirements::css("themes/openstack/css/static.combined.css");
        Requirements::javascript("themes/openstack/javascript/guide.js");
    }


}
