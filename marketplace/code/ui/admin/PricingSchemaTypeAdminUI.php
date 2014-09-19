<?php
final class PricingSchemaTypeAdminUI extends DataExtension {

	private static $searchable_fields =  array('Type');
	/**
	 * @param FieldList $fields
	 * @return FieldList|void
	 */
	public function updateCMSFields(FieldList $fields) {
		$oldFields = $fields->toArray();
		foreach($oldFields as $field){
			$fields->remove($field);
		}

		$fields->push(new LiteralField("Title","<h2>Pricing Schema Type</h2>"));
		$fields->push(new TextField("Type","Type"));
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
		$validator= new RequiredFields(array('Type'));
		return $validator;
	}
}