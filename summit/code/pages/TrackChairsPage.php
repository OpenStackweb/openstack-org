<?php

/*  Used to load the Javascript app that runs the track chairs process
*/

class TrackChairsPage extends Page {

}


class TrackChairsPage_Controller extends Page_Controller {

  public function init() {
        parent::init();
        Requirements::clear();
  }	

}