<?php

/**
 * Class TrainingCourseScheduleAdminUI
 */
class TrainingCourseScheduleAdminUI extends DataExtension {

	public function updateCMSFields(FieldList $fields) {
		$oldFields = $fields->toArray();
		foreach($oldFields as $field){
			$fields->remove($field);
		}

		$CountryCodes = CountryCodes::$iso_3166_countryCodes;
		$CountryCodes[""] = $CountryCodes["unspecified"];
		unset($CountryCodes["unspecified"]);

		$fields->push(new LiteralField("Title","<h2>Course Schedule </h2>"));
		$fields->push(new TextField("City","City"));
		$fields->push(new TextField("State","State"));
		$fields->push(new DropdownField("Country","Country",$CountryCodes));

		if($this->owner->ID > 0 ){
			$config = GridFieldConfig_RecordEditor::create();
			$config->removeComponentsByType('GridFieldAddExistingAutocompleter');
			$times  = new GridField("Times","Times", $this->owner->Times(),$config);
			$fields->push($times);
		}
		return $fields;
	}

	function getCMSValidator()
	{
		return $this->getValidator();
	}

	function getValidator()
	{
		$validator= new RequiredFields(array('City','Country'));
		return $validator;
	}
} 