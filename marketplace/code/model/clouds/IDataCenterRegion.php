<?php
/**
 * Interface IDataCenterRegion
 */
interface IDataCenterRegion extends IEntity {
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
	 * @return string
	 */
	public function getEndpoint();

	/**
	 * @param string $endpoint
	 * @return void
	 */
	public function setEndpoint($endpoint);

	/**
	 * @return ICloudService
	 */
	public function getCloud();

	/**
	 * @param ICloudService $cloud
	 * @return void
	 */
	public function setCloud(ICloudService $cloud);

	/**
	 * @return IDataCenterLocation[]
	 */
	public function getLocations();

	/**
	 * @param IDataCenterLocation $location
	 * @return void
	 */
	public function addLocation(IDataCenterLocation $location);

	/**
	 * @return void
	 */
	public function clearLocations();

	/**
	 * @return string
	 */
	public function getColor();

	/**
	 * @param string $color
	 * @return void
	 */
	public function setColor($color);
} 