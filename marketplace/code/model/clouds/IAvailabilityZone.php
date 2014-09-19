<?php
/**
 * Interface IAvailabilityZone
 */
interface IAvailabilityZone extends IEntity {

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name);

	/**
	 * @return IDataCenterLocation
	 */
	public function getLocation();

	/**
	 * @param IDataCenterLocation $location
	 * @return void
	 */
	public function setLocation(IDataCenterLocation $location);

}