<?php
/**
 * Defines the JobsHolder page type
 */
class EventHolder extends Page {
   private static$db = array(
   );

   private static $has_one = array(
   );
 
   static $allowed_children = array('EventPage');
   /** static $icon = "icon/path"; */
      
}
/**
 * Class EventHolder_Controller
 */
class EventHolder_Controller extends Page_Controller {
	
	function init() {
	    parent::init();
		Requirements::css('events/css/events.css');
		Requirements::javascript('events/js/events.js');
	}
	
	function RandomEventImage(){ 
		$image = Image::get()->filter(array('ClassName:not' => 'Folder'))->where("ParentID = (SELECT ID FROM File WHERE ClassName = 'Folder'
		AND Name = 'EventImages')")->sort('RAND()')->first();
		return $image;
	}
	
	function PastEvents($num = 4) {
		return EventPage::get()->filter(array('EventEndDate:LessThanOrEqual'=>'now()', 'IsSummit'=>1))->sort('EventEndDate')->limit($num);
	}
	
	function FutureEvents($num = 4) {
		return EventPage::get()->filter(array('EventEndDate:GreaterThanOrEqual'=>'now()'))->sort('EventStartDate','ASC')->limit($num);
	}
	
	function PastSummits($num = 4) {
		return EventPage::get()->filter(array('EventEndDate:LessThanOrEqual'=>'now()', 'IsSummit'=>1))->sort('EventEndDate','DESC')->limit($num);
	}


	function FutureSubmits($num = 4) {
		return EventPage::get()->filter(array('EventEndDate:GreaterThanOrEqual'=>'now()', 'IsSummit'=>1))->sort('EventEndDate','ASC')->limit($num);
	}

	function PostEventLink(){
		$page = EventRegistrationRequestPage::get()->first();
		if($page){
			return $page->getAbsoluteLiveLink(false);
		}
		return '#';
	}
}