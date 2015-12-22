<?php

/* 
   Static content page for Austin
*/

class SummitStaticSponsorPage extends SummitPage {

}


class SummitStaticSponsorPage_Controller extends SummitPage_Controller {

  public function init() {
        parent::init();
        Requirements::block("summit/css/combined.css");
        Requirements::css("themes/openstack/static/css/combined.css");
  }


}