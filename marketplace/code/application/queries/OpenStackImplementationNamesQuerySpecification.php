<?php
/**
 * Class OpenStackImplementationNamesQuerySpecification
 */
final class OpenStackImplementationNamesQuerySpecification
	implements IOpenStackImplementationNamesQuerySpecification{

	private $name_pattern;
	public function __construct($name_pattern){
		$this->name_pattern = $name_pattern;
	}

	/**
	 * @return string
	 */
	public function getNamePatternToSearch()
	{
		return $this->name_pattern;
	}

	/**
	 * @return array
	 */
	public function getSpecificationParams()
	{
		return array('name_pattern'=>$this->name_pattern);
	}
}