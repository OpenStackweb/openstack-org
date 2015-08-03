<?php

/*  Used to load the Javascript app that runs the track chairs process
*/

class TrackChairsPage extends Page {

}


class TrackChairsPage_Controller extends Page_Controller {

  public function init() {
  		if(!$this->trackChairCheck()) Security::permissionFailure($this, $body);
        parent::init();
        Requirements::clear();
  }	

  function trackChairCheck() {

  	$member = Member::currentUser();
  	$chair = new SummitTrackChair();

  	if($member) {
  		$chair = SummitTrackChair::get()->filter(array(
  			'MemberID' => $member->ID,
  			'SummitID' => Summit::get_active()->ID
  		));
  	}

  	if($chair->exists()) return true;

  }

}