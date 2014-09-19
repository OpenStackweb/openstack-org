<?php

/**
 * Class FoundationMembersSubscribedToNewsLetterCountQuerySpecification
 */
final class FoundationMembersSubscribedToNewsLetterCountQuerySpecification implements IQuerySpecification {
	/**
	 * @var string
	 */
	private $country;

	/**
	 * @param string $country
	 */
	public function __construct($country = null){
		$this->country = $country;
	}
	/**
	 * @return array
	 */
	public function getSpecificationParams()
	{
		return array($this->country);
	}
}