<?php
/**
 * Interface IPublicCloudFactory
 */
interface IPublicCloudFactory extends ICloudFactory {

	/**
	 * @param string $city
	 * @param string $state
	 * @param string $country
	 * @param float $lat
	 * @param float $lng
	 * @param IDataCenterRegion $region
	 * @return IDataCenterLocation
	 */
	public function buildDataCenterLocation($city,$state,$country,$lat,$lng,IDataCenterRegion $region);

	/**
	 * @param                     $name
	 * @param IDataCenterLocation $location
	 * @return IAvailabilityZone
	 */
	public function buildAZ($name,IDataCenterLocation $location);

	/**
	 * @param string $name
	 * @param string $color
	 * @param string $endpoint
	 * @return IDataCenterRegion
	 */
	public function buildDataCenterRegion($name,$color, $endpoint);
} 