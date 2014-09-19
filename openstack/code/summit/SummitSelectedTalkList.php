<?php
	
class SummitSelectedTalkList extends DataObject {

	static $db = array(
		'Name' => 'Text'
	);
	
	static $has_one = array(
		'SummitCategory' => 'SummitCategory'
	);

	static $has_many = array(
		'SummitSelectedTalks' => 'SummitSelectedTalk'
	);

	function SortedTalks() {
      return SummitSelectedTalk::get()->filter(array( 'SummitSelectedTalkListID' => $this->ID, 'Order:not' => 0))->sort('Order','ASC');
    }

	function UnsortedTalks() {
      return SummitSelectedTalk::get()->filter(array('SummitSelectedTalkListID' => $this->ID,  'Order' => 0))->sort('Order','ASC');
    }

    function UnusedPostions() {

      // Define the columns
      $columnArray = array();

      $NumSlotsTaken = $this->SummitSelectedTalks()->Count();
      $NumSlotsAvailable = $this->SummitCategory()->NumSessions - $NumSlotsTaken;

      $list = new ArrayList();


      for ($i = 0; $i < $NumSlotsAvailable; $i++) {
      	$data = array('Name' => 'Available Slot');
      	$list->push(new ArrayData($data));
      }

      return $list; 

    }

}