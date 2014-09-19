<?php
/**
 * Class CompanyCountQuerySpecification
 */
final class CompanyCountQuerySpecification implements IQuerySpecification {

	/**
	 * @var string
	 */
	private $member_level;

	/**
	 * @var string
	 */
	private $country;

	/**
	 * @param string $member_level
	 * @param string $country
	 */
	public function __construct($member_level = null, $country = null){
		$this->member_level = $member_level;
		$this->country = $country;
	}

	/**
	 * @return array
	 */
	public function getSpecificationParams()
	{
		return array($this->member_level, $this->country );
	}
}