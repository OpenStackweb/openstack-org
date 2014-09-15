<?php

/**
 * Class NewsRequestForm
 */
final class NewsRequestForm extends SafeXSSForm {

	function __construct($controller, $name, $use_actions = true) {
		$fields = new FieldSet;
		//madatory fields
		$fields->push(new TextField('headline','Headline'));
		$fields->push(new TextareaField('summary','Summary',2,2));
		$fields->push(new TextField('tags','Tags'));
		$fields->push($date = new DateField('date','Date'));
        $date->addExtraClass('date inline');
        $fields->push($date_embargo = new DateField('date_embargo','Embargo Date'));
        $date_embargo->addExtraClass('date inline');
        $fields->push($updated = new DatetimeField_Readonly('date_updated','Last Updated'));
        $updated->addExtraClass('inline');
        $fields->push(new LiteralField('clear', '<div class="clear"></div>'));
        //optional fields
        $fields->push($body = new TextareaField('body','Body'));
        $fields->push(new TextField('link','Link'));
        $fields->push(new FileField('document','Document'));
        $fields->push(new LiteralField('break', '<br/>'));
        $fields->push($image = new FileField('image','Image'));
        $fields->push(new LiteralField('break', '<br/>'));
        $fields->push($date_expire = new TextField('date_expire','Date Expire'));
        $date_expire->addExtraClass('date');
        // submitter fields
        $fields->push(new LiteralField('break', '<br/><hr/>'));
        $fields->push(new LiteralField('title', '<h2>Submitter</h2>'));
        $fields->push(new TextField('submitter_first_name','First Name'));
        $fields->push(new TextField('submitter_last_name','Last Name'));
        $fields->push(new TextField('submitter_email','Email'));
        $fields->push(new TextField('submitter_company','Company'));
        $fields->push(new TextField('submitter_phone','Phone'));

		// Create action
		$actions = new FieldSet();
	    $actions->push(new FormAction('saveNewsArticle', 'Save'));

		// Create validators
		$validator = new ConditionalAndValidationRule(array(new RequiredFields('headline','summary','tags','date','date_embargo')));
		$validator->setJavascriptValidationHandler('none');
        $this->addExtraClass('news-registration-form');
		parent::__construct($controller, $name, $fields, $actions, $validator);
	}

	function forTemplate() {
		return $this->renderWith(array(
			$this->class,
			'Form'
		));
	}

	function submit($data, $form) {
		// do stuff here
	}
}