<?php
final class OpenStackApiVersionAdminUI extends DataExtension {

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
		$fields->push(new LiteralField("Title","<h2>OpenStack Api Version</h2>"));
		$fields->push(new TextField("Version","Version"));

		$fields->push( new DropdownField(
				'Status',
				'Status',
				$this->owner->dbObject('Status')->enumValues()
		));

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
		$validator= new RequiredFields(array('Version'));
		return $validator;
	}
}