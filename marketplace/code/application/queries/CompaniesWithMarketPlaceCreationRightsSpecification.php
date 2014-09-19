<?php
/**
 * Class CompaniesWithMarketPlaceCreationRightsSpecification
 */
final class CompaniesWithMarketPlaceCreationRightsSpecification
implements IQuerySpecification
{
	private $marketplace_type;
	/**
	 * @param string $marketplace_type
	 */
	public function __construct($marketplace_type){
		$this->marketplace_type = $marketplace_type;

	}

	/**
	 * @return array
	 */
	public function getSpecificationParams(){
		return array($this->marketplace_type);
	}
}