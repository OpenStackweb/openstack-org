<?php

class UserStoriesLink extends DataObject {

	static $db = array(
		'LinkName' => 'Text',
		'LinkURL' => 'Text',
		'Description' => 'Text',
	);

	static $has_one = array(
		'UserStory' => 'UserStory'
	);

	static $summary_fields = array(
		'UserStory.Title' => 'Story',
		'LinkName' => 'Link Name', 
		'LinkURL' => 'URL'
	);

	static $singular_name = 'Link';
	static $plural_name = 'Links';

	function getCMSFields() {
		$fields = parent::getCMSFields();
			 
		$user_stories = UserStory::get();
		if ($user_stories) {
				$user_stories = $user_stories->map('ID', 'Title', '(Select one)', true);
		}
		
		$fields->addFieldstoTab('Root.Main', 
			array(
				new DropdownField('UserStoryID', 'User Story', $user_stories),
				new TextField('LinkName', 'Link Name'),
				new TextField('LinkURL', 'Full URL'),
				new TextField('Description', 'Description'),
				new HiddenField('SortOrder')
			)
		);
		 
		return $fields;
	}
}