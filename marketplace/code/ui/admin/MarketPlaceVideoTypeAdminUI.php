<?php
/**
 * Class MarketPlaceVideoTypeAdminUI
 */
final class MarketPlaceVideoTypeAdminUI
	extends DataExtension {

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
		$fields->push(new LiteralField("Title","<h2>MarketPlace Video Type</h2>"));
		$fields->push(new TextField("Type","Type"));
		$fields->push(new TextField("Title","Title"));
		$fields->push(new TextField("Description","Description"));
		$fields->push(new TextField("MaxTotalVideoTime","Max. Video Length (Seconds)"));
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
		$validator= new RequiredFields(array('Type','Title','Description','MaxTotalVideoTime'));
		return $validator;
	}

} 