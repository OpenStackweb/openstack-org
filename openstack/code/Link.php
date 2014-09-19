<?php
	
class Link extends DataObject {

	static $db = array(
		'Label' => 'Text',
		'URL' => 'Text',
		'Description' => 'HTMLText'
	);
	
	static $has_one = array(
		'Page' => 'Page'
	);
	
	static $singular_name = 'Link';
	static $plural_name = 'Links';
	
	static $summary_fields = array( 
	      'Label' => 'Label', 
	      'URL' => 'URL'
	   );
	
	function getCMSFields() {
		$fields = new FieldList (
			new TextField('Label','Label this link (this will be the text displayed):'),
			new TextField ('URL','Full URL (ex: http://www.photos.com/photo.jpg) for image'),
			new TextField ('Description','Short description / text for this link (optional)')
		);
		return $fields;
	}
	
}