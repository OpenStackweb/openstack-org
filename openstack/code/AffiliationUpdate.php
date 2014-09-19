<?php
	
class AffiliationUpdate extends DataObject {

	static $db = array(
		'NewAffiliation' => 'Text',
		'OldAffiliation' => 'Text'
	);
	
	static $has_one = array(
		'Member' => 'Member'
	);
	
	static $singular_name = 'AffiliationUpdate';
	static $plural_name = 'AffiliationUpdates';
		
}