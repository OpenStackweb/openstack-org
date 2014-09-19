<?php

class AddInvolvementTypeForm extends Form
{

	function __construct($controller, $name)
	{


		$NameField = new TextField('Name', 'Involvement Level:');

		$fields = new FieldList(
			$NameField
		);

		$actions = new FieldList(
			new FormAction('submit', 'Add Involvement Level')
		);

		parent::__construct($controller, $name, $fields, $actions);

		// Create Validators
		$validator = new RequiredFields('Name');


		$this->disableSecurityToken();


	}

	function forTemplate()
	{
		return $this->renderWith(array(
			$this->class,
			'Form'
		));
	}

	function submit($data, $form)
	{
		$involvementType = new InvolvementType();
		$form->saveInto($involvementType);
		$involvementType->write();
		Controller::curr()->redirect('/sangria/');
	}

}