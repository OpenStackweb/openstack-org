<?php
	
class Feature extends DataObject {

	static $db = array(
		'Feature' => 'HTMLText',
		'URL' => 'Text',
		'Benefit' => 'HTMLText',
		'Roadmap' => 'Boolean'
	);
	
	static $has_one = array(
		'ProductPage' => 'ProductPage'
	);
	
	static $singular_name = 'Feature';
	static $plural_name = 'Features';
	
	static $summary_fields = array( 
	      'Feature' => 'Feature', 
	      'Benefit' => 'Benefit',
	      'RoadmapNice' => 'Roadmap'
	   );
	
	function getCMSFields() {
		$fields = new FieldList (
			new SimpleTinyMCEField ('Feature','Feature'),
			new SimpleTinyMCEField ('Benefit','Benefit'), 
			new TextField ('URL','Link To More Information (URL)'),
			new CheckboxField ('Roadmap','This is an upcoming (roadmap) feature')
		);
		return $fields;
	}
	
	
	//Generate Yes/No for DOM / Complex Table Field 
	public function RoadmapNice() { 
	   return $this->Roadmap ? 'Yes' : 'No'; 
	}	

}