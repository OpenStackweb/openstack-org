<?php
/**
 * Class MarketplaceContractTemplate
 */
class MarketplaceContractTemplate extends ContractTemplate{

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $has_one = array(
		'MarketPlaceType' => 'MarketPlaceType',
	);

	public static $summary_fields = array(
		'Name'                 => 'Name',
		'PDF.Name'             => 'Pdf filename',
		'MarketPlaceType.Name' => 'Marketplace Type',
	);

	static $searchable_fields = array(
		"Name" => array(
			"field"   => "TextField"
		),

		"PDF.Name" => array(
			"field"   => "TextField"
		),

		"MarketPlaceType.Name" => array(
			'title'  => 'MarketPlace Type',
			"field"  => 'DropdownField',
			'filter' => 'PartialMatchfilter'
		)
	);

	/**
	 * @override
	 * add the dataset to marketplace dropdown
	 * @return SearchContext
	 */
	public function getDefaultSearchContext() {
		$res                = parent::getDefaultSearchContext();
		$fields             = $res->getSearchFields();
		$marketplace_filter = $fields->dataFieldByName('MarketPlaceType__Name');
		if(!is_null($marketplace_filter))
			$marketplace_filter->setSource(MarketPlaceType::get()->map("Name", "Name"));
		return $res;
	}

	/**
	 * @return FieldList
	 */

	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$fields->push(new DropdownField(
			'MarketPlaceTypeID',
			'MarketPlaceType',
			MarketPlaceType::get()->map("ID", "Name")));
		return $fields;
	}

	function getValidator()
	{
		$validator_required       = new RequiredFields(array('Name','Duration','MarketPlaceTypeID'));
		$int_rule                 = new NetefxValidatorRuleGREATER('Duration','Insert a number greater than 0', null, 0);
		$validator_integer_fields = new NetefxValidator($int_rule);
		return new ConditionalAndValidationRule(array($validator_required,$validator_integer_fields));
	}

} 