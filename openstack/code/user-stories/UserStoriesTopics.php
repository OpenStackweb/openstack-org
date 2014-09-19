<?php

class UserStoriesTopics extends DataObject {

	static $db = array(
		'Topic' => 'Text',
	);

	static $summary_fields = array(
		'Topic' => 'Topic'
	);

	static $singular_name = 'Topic';
	static $plural_name = 'Topics';


	function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldstoTab('Root.Main', 
			array(
				new TextField('Topic', 'Topic'),
			)
		);

		return $fields;
	}

}