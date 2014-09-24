<?php

/**
 * Interface IMarketPlaceAllowedInstance
 */
interface IMarketPlaceAllowedInstance extends IEntity {
	/**
	 * @return int
	 */
	public function getMaxInstances();

	/**
	 * @param int $max_instances
	 * @return void
	 */
	public function setMaxInstances($max_instances);


	/**
	 * @param IMarketPlaceType $type
	 * @return void
	 */
	public function setType(IMarketPlaceType $type);

	/**
	 * @return IMarketPlaceType
	 */
	public function getType();


	/**
	 * @param ICompany $company
	 * @return void
	 */
	public function setCompany(ICompany $company);


	/**
	 * @return ICompany
	 */
	public function getCompany();
} 