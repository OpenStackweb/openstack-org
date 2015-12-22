<?php

/* 
   Static categories page for Austin
*/

class SummitStaticCategoriesPage extends SummitPage {

}


class SummitStaticCategoriesPage_Controller extends SummitPage_Controller {

  public function init() {
        parent::init();
        Requirements::block("summit/css/combined.css");
        Requirements::css("themes/openstack/static/css/combined.css");
  }

}