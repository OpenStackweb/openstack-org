<?php

class UserStoriesFeatured extends DataObject {

	static $has_one = array(
		'UserStory' => 'UserStory',
		'UserStoriesIndustry' => 'UserStoriesIndustry'
	);

	static $summary_fields = array(
		'UserStory.Title' => 'Story',
		'UserStoriesIndustry.IndustryName' => 'Industry', 
	);

	static $singular_name = 'Featured Story';
	static $plural_name = 'Featured Stories';

	function getCMSFields() {
		$fields = parent::getCMSFields();
			 
		$user_stories = UserStory::get();
		if ($user_stories) {
				$user_stories = $user_stories->map('ID', 'Title', '(Select one)', true);
		}

		$industries = UserStoriesIndustry::get();
		if ($industries) {
				$industries = $industries->map('ID', 'IndustryName', '(Select one)', true);
		}
		
		$fields->addFieldstoTab('Root.Main', 
			array(
				new DropdownField('UserStoryID', 'User Story', $user_stories),
				new DropdownField('UserStoriesIndustryID', 'Industry', $industries)
			)
		);
		 
		return $fields;
	}
}