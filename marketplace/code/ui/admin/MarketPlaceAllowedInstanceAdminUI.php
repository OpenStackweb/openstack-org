<?php

/**
 * Class MarketPlaceAllowedInstanceAdminUI
 */
final class MarketPlaceAllowedInstanceAdminUI extends DataExtension {

	public function updateCMSFields(FieldList $fields) {
		$oldFields = $fields->toArray();
		foreach($oldFields as $field){
			$fields->remove($field);
		}

		$fields->push(new TextField("MaxInstances","Max. Instances"));

		$companies = Company::get();
		if($companies){
			$fields->push($ddl = new DropdownField(
				'CompanyID',
				'Company',
				$companies->map("ID", "Name")));
			$ddl->setEmptyString("Please Select a Company");
		}


		$market_place_types = MarketPlaceType::get();
		if($market_place_types){
			$fields->push($ddl = new DropdownField(
				'MarketPlaceTypeID',
				'MarketPlaceType',
				$market_place_types->map("ID", "Name")));

			$ddl->setEmptyString( "Please Select a Market Place Type");
		}

		return $fields;
	}

	function getCMSValidator()
	{
		return $this->getValidator();
	}

	function getValidator()
	{
		$validator= new RequiredFields(array('CompanyID','MarketPlaceTypeID','MaxInstances'));
		return $validator;
	}
} 