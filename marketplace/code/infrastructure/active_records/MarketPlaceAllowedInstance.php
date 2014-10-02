<?php

/**
 * Class MarketPlaceAllowedInstance
 */
final class MarketPlaceAllowedInstance extends DataObject implements  IMarketPlaceAllowedInstance {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'MaxInstances' => 'Int',
	);

	static $indexes = array(
		'Type' => array('type'=>'unique', 'value'=>'MarketPlaceTypeID,CompanyID')
	);

	static $has_one = array(
		'MarketPlaceType'  => 'MarketPlaceType',
		'Company' => 'Company',
	);


	public static $summary_fields = array(
		'MaxInstances',
		'MarketPlaceType.Name',
		'Company.Name',
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	public function getMaxInstances()
	{
		return (int)$this->getField('MaxInstances');
	}

	public function setMaxInstances($max_instances)
	{
		$this->setField('MaxInstances',$max_instances);
	}


	/**
	 * @param IMarketPlaceType $type
	 * @return void
	 */
	public function setType(IMarketPlaceType $type)
	{
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'MarketPlaceType')->setTarget($type);
	}

	/**
	 * @return IMarketPlaceType
	 */
	public function getType()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'MarketPlaceType')->getTarget();
	}


	/**
	 * @param ICompany $company
	 * @return void
	 */
	public function setCompany(ICompany $company)
	{
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'Company')->setTarget($company);
	}

	/**
	 * @return ICompany
	 */
	public function getCompany()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Company')->getTarget();
	}
} 