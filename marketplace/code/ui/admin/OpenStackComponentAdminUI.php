<?php
final class OpenStackComponentAdminUI extends DataExtension {

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
		$fields->push(new LiteralField("Title","<h2>OpenStack Component</h2>"));
		$fields->push(new TextField("Name","Name"));
		$fields->push(new TextField("CodeName","Code Name"));
		$fields->push(new TextField("Description","Description"));
		$fields->push(new CheckboxField('SupportsVersioning','Supports Versioning?'));
		$fields->push(new CheckboxField('SupportsExtensions','Supports Extensions?'));
		if($this->owner->getSupportsVersioning()){

			$versions_config = new GridFieldConfig_RecordEditor();

			$versions = new GridField("Versions", "Versions", $this->owner->Versions(), $versions_config);

			$fields->push($versions);
		}
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
		$validator= new RequiredFields(array('Name','CodeName'));
		return $validator;
	}

}