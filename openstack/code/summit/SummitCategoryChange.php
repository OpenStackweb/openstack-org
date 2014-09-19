<?php
	
class SummitCategoryChange extends DataObject {

	static $db = array(
		'Approved' => 'Boolean',
		'NewCategoryID' => 'Int'
	);
	
	static $has_one = array(
		'Talk' => 'Talk',
		'Approver' => 'Member',
		'Requester' => 'Member'
	);

}