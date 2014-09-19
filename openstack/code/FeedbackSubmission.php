<?php
	
class FeedbackSubmission extends DataObject {

	static $db = array(
		'Content' => 'HTMLText',
		'Page' => 'Text'
	);
	
	static $has_one = array(
	);
	
	static $singular_name = 'Feedback Submission';
	static $plural_name = 'Feedback Submissions';
	
}