<?php
/**
 * Class SpokenLanguageAdminUI
 */
final class SpokenLanguageAdminUI
	extends DataExtension {

	private static $searchable_fields =  array('Name');
	/**
	 * @param FieldList $fields
	 * @return FieldList|void
	 */
	public function updateCMSFields(FieldList $fields) {
		$oldFields = $fields->toArray();
		foreach($oldFields as $field){
			$fields->remove($field);
		}
		$fields->push(new LiteralField("Title","<h2>Spoken Language</h2>"));
		$fields->push(new TextField("Name","Name"));
		return $fields;
	}

	public function onBeforeWrite(){
		//create group here?
		parent::onBeforeWrite();
	}

	function getCMSValidator()
	{
		return $this->getValidator();
	}

	function getValidator()
	{
		$validator= new RequiredFields(array('Name'));
		return $validator;
	}
}