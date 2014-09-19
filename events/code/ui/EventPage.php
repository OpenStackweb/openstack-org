<?php
/**
 * Class EventPage
 */
class EventPage
	extends Page
	implements IEvent {

	private static $db = array(
    	'EventStartDate'      => 'Date',
    	'EventEndDate'        => 'Date',
    	'EventLink'           => 'Text',
    	'EventLinkLabel'      => 'Text',
    	'EventLocation'       => 'Text',
    	'EventSponsor'        => 'Text',
    	'EventSponsorLogoUrl' => 'Text',
    	'IsSummit'            => 'Boolean'
   );

	private static $has_one = array();

   	   
 	function getCMSFields() {
    	$fields = parent::getCMSFields();
    	
    	// the date field is added in a bit more complex manner so it can have the dropdown date picker
    	$EventStartDate = new DateField('EventStartDate','First Day of Event');
    	$EventStartDate->setConfig('showcalendar', true);
    	$EventStartDate->setConfig('showdropdown', true);
		$fields->addFieldToTab('Root.Main', $EventStartDate, 'Content');
		
		// same things for the event end date
		$EventEndDate = new DateField('EventEndDate','Last Day of Event');
		$EventEndDate->setConfig('showcalendar', true);
		$EventEndDate->setConfig('showdropdown', true);
		$fields->addFieldToTab('Root.Main', $EventEndDate, 'Content');
		    	
    	$fields->addFieldToTab('Root.Main', new TextField('EventLink','Event Button Link (URL)'), 'Content');
    	$fields->addFieldToTab('Root.Main', new TextField('EventLinkLabel','Event Button Label'), 'Content');
    	
    	$fields->addFieldToTab('Root.Main', new TextField('EventLocation','Event Location'), 'Content');
    	$fields->addFieldToTab('Root.Main', new TextField('EventSponsor','Event Sponsor'), 'Content');
    	$fields->addFieldToTab('Root.Main', new TextField('EventSponsorLogoUrl','URL of the Event Sponsor Logo'), 'Content');
    	
    	$fields->addFieldToTab('Root.Main', new CheckboxField ('IsSummit','Official OpenStack Summit Event'), 'Content');
    	    	    	
		// remove unneeded fields 
		$fields->removeFieldFromTab("Root.Main","MenuTitle");
		
		// rename fields
		$fields->renameField("Content", "Event Page Content");
		$fields->renameField("Title", "Event Title");
    
    	return $fields;
 	}
 	
	public function formatDateRange() {
		$startDateArray = date_parse($this->EventStartDate);
		$endDateArray = date_parse($this->EventEndDate);
		
		if ($startDateArray["year"] == $endDateArray["year"] 
				&& $startDateArray["month"] == $endDateArray["month"] 
				&& $startDateArray["day"] == $endDateArray["day"]) {
			// single day range
			return date('M d, Y',strtotime($this->EventStartDate));
		} else if ($startDateArray["year"] == $endDateArray["year"] 
				&& $startDateArray["month"] == $endDateArray["month"]) {
			// multi-day, single month range
			$value = date('M d - ',strtotime($this->EventStartDate));
			$value .= date('d, Y',strtotime($this->EventEndDate));
			return $value;
		} else if ($startDateArray["year"] == $endDateArray["year"]) {
			// same year, spanning months (there days as well)
			$value = date('M d - ',strtotime($this->EventStartDate));
			$value .= date('M d, Y',strtotime($this->EventEndDate));
			return $value;
		} else {
			// must be different years (therefore months and days as well)
			$value = date('M d, Y - ',strtotime($this->EventStartDate));
			$value .= date('M d, Y',strtotime($this->EventEndDate));
			return $value;
		}
	}

	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}
}

/**
 * Class EventPage_Controller
 */
class EventPage_Controller extends Page_Controller {
	public function DateSortedChildren(){
	   $children = $this->Children(); 
	   if(!$children) 
	      return null; 
	          
	   $children->sort('EventStartDate', 'DESC');
	   return $children; 
	}
}