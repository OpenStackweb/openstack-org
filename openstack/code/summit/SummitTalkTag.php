<?php
	
class SummitTalkTag extends DataObject {

	static $db = array(
		'Name' => 'Text'
	);
	
	static $has_one = array(
		'Summit' => 'Summit'
	);

}	


?>