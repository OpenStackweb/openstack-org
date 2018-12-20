<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
/**
 * Class EventRegistrationRequestForm
 */
final class EventRegistrationRequestForm extends SafeXSSForm {

	function __construct($controller, $name, $use_actions = true) {
        $event_manager = new EventManager(
            new SapphireEventRepository,
            new EventRegistrationRequestFactory,
            null,
            new SapphireEventPublishingService,
            new EventValidatorFactory,
            SapphireTransactionManager::getInstance()
        );

		$fields = new FieldList;
		//point of contact
		$fields->push(new TextField('point_of_contact_name','Name'));
		$fields->push(new EmailField('point_of_contact_email','Email'));
		//main info
		$fields->push(new TextField('title','Title'));
		$fields->push(new TextField('url','Url'));

        $types = $event_manager->getAllTypes();
        foreach($types as $type) {
            $options[$type] = $type;
        }

        $categoryField = new DropdownField('category','Category', $options);
        $categoryField->setEmptyString('-- select a category --');
        $fields->push($categoryField);
		//location
		$fields->push(new TextField('city','City'));
		$fields->push(new TextField('state','State'));
        $countryField = new CountryDropdownField('country','Country');
        $countryField->setEmptyString('-- select a country --');
		$fields->push($countryField);
		//duration
		$fields->push($start_date = new TextField('start_date','Start Date'));
		$fields->push($end_date   = new TextField('end_date','End Date'));

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
		$validator = new ConditionalAndValidationRule(
		    array(
		        new HtmlPurifierRequiredValidator('title','city'),
                new RequiredFields('point_of_contact_name','point_of_contact_email','start_date','end_date','country')
            )
        );
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