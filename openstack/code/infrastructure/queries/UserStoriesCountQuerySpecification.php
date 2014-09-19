<?php
/**
 * Class UserStoriesCountQuerySpecification
 */
final class UserStoriesCountQuerySpecification implements IQuerySpecification {

	/**
	 * @var bool
	 */
	private $featured;

	/**
	 * @param bool $featured
	 */
	public function __construct($featured){
		$this->featured = $featured;
	}
	/**
	 * @return array
	 */
	public function getSpecificationParams()
	{
		return array($this->featured);
	}
}