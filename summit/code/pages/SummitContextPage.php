<?php

/* 
   Used to set a clean, stripped-down template for legal and terms pages
   These pages contain no nav and just a back button
*/

class SummitContextPage extends SummitPage {

}


class SummitContextPage_Controller extends SummitPage_Controller {

    public function init()
    {
        parent::init();
        Requirements::block("summit/css/combined.css");
        Requirements::css("themes/openstack/static/css/combined.css");
    }

}