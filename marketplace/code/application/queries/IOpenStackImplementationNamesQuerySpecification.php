<?php

/**
 * Interface IOpenStackImplementationNamesQuerySpecification
 */
interface IOpenStackImplementationNamesQuerySpecification extends IQuerySpecification{
	/**
	 * @return string
	 */
	public function getNamePatternToSearch();
}