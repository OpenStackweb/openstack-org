<?php
/*
  City Guide
*/
class SummitStaticCityGuide extends SummitPage {
}

class SummitStaticCityGuide_Controller extends SummitPage_Controller {

    public function init()
    {
        parent::init();
        Requirements::css("summit/css/cityguide.css");
    }
}
