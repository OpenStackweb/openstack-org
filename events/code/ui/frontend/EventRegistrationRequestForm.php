<?php

/**
 * Class EventRegistrationRequestForm
 */
final class EventRegistrationRequestForm extends SafeXSSForm {

	function __construct($controller, $name, $use_actions = true) {
		$fields = new FieldList;
		//point of contact
		$fields->push(new TextField('point_of_contact_name','Name'));
		$fields->push(new EmailField('point_of_contact_email','Email'));
		//main info
		$fields->push(new TextField('title','Title'));
		$fields->push(new TextField('url','Url'));
		//location
		$fields->push(new TextField('city','City'));
		$fields->push(new TextField('state','State'));
		$fields->push(new CountryDropdownField('country','Country'));
		//duration
		$fields->push($start_date = new TextField('start_date','Start Date'));
		$fields->push($end_date   = new TextField('end_date','End Date'));
		$start_date->addExtraClass('date');
		$end_date->addExtraClass('date');

		// Guard against automated spam registrations by optionally adding a field
		// that is supposed to stay blank (and is hidden from most humans).
		// The label and field name are intentionally common ("username"),
		// as most spam bots won't resist filling it out. The actual username field
		// on the forum is called "Nickname".
		$fields->push(new TextField('user_name','UserName'));
		// Create action
		$actions = new FieldList();
		if($use_actions)
			$actions->push(new FormAction('saveEventRegistrationRequest', 'Save'));
		// Create validators
		$validator = new ConditionalAndValidationRule(array(new HtmlPurifierRequiredValidator('title','city'), new RequiredFields('point_of_contact_name','point_of_contact_email','start_date','end_date','country')));
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