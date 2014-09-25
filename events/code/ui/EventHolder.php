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

	private static $allowed_actions = array (
		'AjaxFutureEvents',
		'AjaxFutureSummits',
		'AjaxPastSummits',
	);


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
	

	function FutureEvents($num) {
		return EventPage::get()->filter(array('EventEndDate:GreaterThanOrEqual'=>'now()'))->sort('EventStartDate','ASC')->limit($num);
	}

    function PastSummits($num) {
	    return EventPage::get()->filter(array('EventEndDate:LessThanOrEqual'=>'now()', 'IsSummit'=>1))->sort('EventEndDate','DESC')->limit($num);
    }


    function FutureSummits($num) {
	    return EventPage::get()->filter(array('EventEndDate:GreaterThanOrEqual'=>'now()', 'IsSummit'=>1))->sort('EventStartDate','ASC')->limit($num);
    }

    public function getEvents($num = 4, $type) {
        $output = '';

        switch ($type) {
            case 'future_events':
                $events = $this->FutureEvents($num);
                break;
            case 'future_summits':
                $events = $this->FutureSummits($num);
                break;
            case 'past_summits':
                $events = $this->PastSummits($num);
                break;
        }

        if ($events) {
            foreach ($events as $key => $event) {
                $first = ($key == 0);
                $data = array('IsEmpty'=>0,'IsFirst'=>$first);

                $output .= $event->renderWith('EventHolder_event', $data);
            }
        } else {
            $data = array('IsEmpty'=>1);
            $output .= Page::renderWith('EventHolder_event', $data);
        }

        return $output;
    }

    function AjaxFutureEvents() {
        return $this->getEvents(100,'future_events');
    }

    function AjaxFutureSummits() {
        return $this->getEvents(5,'future_summits');
    }

    function AjaxPastSummits() {
        return $this->getEvents(5,'past_summits');
    }

	function PostEventLink(){
		$page = EventRegistrationRequestPage::get()->first();
		if($page){
			return $page->getAbsoluteLiveLink(false);
		}
		return '#';
	}
}