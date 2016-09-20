<?php

/* 
   Static content page for Austin
*/

class SummitStaticAcademyPage extends SummitPage {

}


class SummitStaticAcademyPage_Controller extends SummitPage_Controller {

    public function init()
    {
        parent::init();
        Requirements::css("themes/openstack/static/css/combined.css");
    }


}
