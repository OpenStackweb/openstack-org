<?php

class Announcement extends DataObject{

	private static $db = array(
			'Content' => 'HTMLText',
			'SortOrder' => 'Int'
	);

	private static $default_sort = 'SortOrder';

	private static $singular_name = 'Announcement';
	private static $plural_name = 'Announcements';
	
	
	static $has_one = array(
			'MarketingPage' => 'MarketingPage'
	);
	
	function getCMSFields(){
		return new FieldList(array(
				new HtmlEditorField('Content')
		));
	}
	
	function getValidator()
	{
		return new RequiredFields(array('Content'));
	}
}