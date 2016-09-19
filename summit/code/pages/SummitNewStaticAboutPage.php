<?php

/* 
   Static content page for Austin
*/

class SummitNewStaticAboutPage extends SummitPage {

}


class SummitNewStaticAboutPage_Controller extends SummitPage_Controller {

    public function init()
    {
        $this->top_section = 'full';
        parent::init();
        Requirements::block("summit/css/combined.css");
        Requirements::css("themes/openstack/static/css/combined.css");
    }


}