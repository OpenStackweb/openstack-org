<?php
/**
 * Interface IDataCenterLocation
 */
interface IDataCenterLocation extends IEntity {

	/**
	 * @param string $country
	 * @return void
	 */
	public function setCountry($country);

	/**
	 * @return string
	 */
	public function getCountry();

	/**
	 * @param string $city
	 * @return void
	 */
	public function setCity($city);

	/**
	 * @return string
	 */
	public function getCity();


	/**
	 * @return string
	 */
	public function getState();

	/**
	 * @param string $state
	 * @return void
	 */
	public function setState($state);

	/**
	 * @param ICloudService $cloud_service
	 * @return void
	 */
	public function setCloudService(ICloudService $cloud_service);

	/**
	 * @return ICloudService
	 */
	public function getCloudService();

	/**
	 * @return IAvailabilityZone[]
	 */
	public function getAvailabilityZones();

	/**
	 * @return void
	 */
	public function clearAvailabilityZones();

	/**
	 * @param IAvailabilityZone $az
	 * @return void
	 */
	public function addAvailabilityZone(IAvailabilityZone $az);

	/**
	 * @param float $lng
	 * @return void
	 */
	public function setLng($lng);

	/**
	 * @return float
	 */
	public function getLng();

	/**
	 * @param float $lat
	 * @return void
	 */
	public function setLat($lat);

	/**
	 * @return float
	 */
	public function getLat();

	/**
	 * @return IDataCenterRegion
	 */
	public function getDataCenterRegion();

	/**
	 * @param IDataCenterRegion $region
	 * @return void
	 */
	public function setDataCenterRegion(IDataCenterRegion $region);
} 