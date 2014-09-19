<?php

class PresentationFlagForm extends Form
{

	function __construct($controller, $name)
	{


		$FlagMessageField = new TextField('FlagComment', 'Your Comment');

		$fields = new FieldList(
			$FlagMessageField
		);

		$searchButton = new FormAction('doFlag', 'Save');
		$searchButton->addExtraClass('btn btn-default btn-sm');

		$actions = new FieldList(
			$searchButton
		);

		parent::__construct($controller, $name, $fields, $actions);
	}

	function forTemplate()
	{
		return $this->renderWith(array(
			$this->class,
			'Form'
		));
	}

}