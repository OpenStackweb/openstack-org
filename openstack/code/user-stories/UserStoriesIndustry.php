<?php

class UserStoriesIndustry extends DataObject {

	static $db = array(
		'IndustryName' => 'Text',
		'Active' => 'Boolean'
	);

	static $summary_fields = array(
		'IndustryName' => 'Industry Name', 
		'Active' => 'Active'
	);

	static $singular_name = 'Industry';
	static $plural_name = 'Industries';


	function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldstoTab('Root.Main', 
			array(
				new TextField('IndustryName', 'Name'),
				new CheckboxField('Active', 'Active'),
				new HiddenField('SortOrder')
			)
		);

		return $fields;
	}

	function FeaturedStory(){
		return UserStoriesFeatured::get()->filter('UserStoriesIndustryID',$this->ID);
	}

	function Stories(){
		return UserStory::get()->filter('UserStoriesIndustryID',$this->ID);
	}

}