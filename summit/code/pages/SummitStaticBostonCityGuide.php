<?php
/*
  City Guide
*/
class SummitStaticBostonCityGuide extends SummitPage {
}

class SummitStaticBostonCityGuide_Controller extends SummitPage_Controller {

    public function init()
    {
        parent::init();
        Requirements::css("summit/css/cityguide.css");
    }
}
