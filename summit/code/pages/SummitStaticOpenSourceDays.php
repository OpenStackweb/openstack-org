<?php
/*
   Static content page for Austin
*/
class SummitStaticOpenSourceDays extends SummitPage {
}

class SummitStaticOpenSourceDays_Controller extends SummitPage_Controller {

    public function init()
    {
        parent::init();
        Requirements::css("summit/css/opensourceday.css");
    }
}
