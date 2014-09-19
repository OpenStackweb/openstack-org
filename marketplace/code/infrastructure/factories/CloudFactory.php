<?php
/**
 * Class CloudFactory
 */
abstract class CloudFactory
	extends OpenStackImplementationFactory
	implements ICloudFactory {

	/**
	 * @param int                         $coverage_percent
	 * @param IReleaseSupportedApiVersion $release_supported_api_version
	 * @param IOpenStackImplementation    $implementation
	 * @return IOpenStackImplementationApiCoverage|CloudServiceOffered
	 */
	public function buildCapability($coverage_percent, IReleaseSupportedApiVersion $release_supported_api_version, IOpenStackImplementation $implementation)
	{
		$service = new CloudServiceOffered;
		$service->setCoveragePercent($coverage_percent);
		$service->setReleaseSupportedApiVersion($release_supported_api_version);
		$service->setImplementation($implementation);
		return $service;
	}

	/**
	 * @param $id
	 * @return IPricingSchemaType
	 */
	public function buildPricingSchemaById($id){
		$pricing_schema = new PricingSchemaType;
		$pricing_schema->ID = $id;
		return $pricing_schema;
	}

	/**
	 * @param string $city
	 * @param string $state
	 * @param string $country
	 * @param float $lat
	 * @param float $lng
	 * @param IDataCenterRegion $region
	 * @return IDataCenterLocation
	 */
	public function buildDataCenterLocation($city,$state,$country,$lat,$lng,IDataCenterRegion $region) {
		$location = new DataCenterLocation;
		$location->setCity($city);
		$location->setState($state);
		$location->setCountry($country);
		$location->setLat($lat);
		$location->setLng($lng);
		$region->addLocation($location);
		$location->setDataCenterRegion($region);
		return $location;
	}

	/**
	 * @param                     $name
	 * @param IDataCenterLocation $location
	 * @return IAvailabilityZone
	 */
	public function buildAZ($name,IDataCenterLocation $location){
		$az = new AvailabilityZone;
		$az->setName($name);
		$az->setLocation($location);
		$location->addAvailabilityZone($az);
		return $az;
	}

	/**
	 * @param string $name
	 * @param string $color
	 * @param string $endpoint
	 * @return IDataCenterRegion
	 */
	public function buildDataCenterRegion($name, $color, $endpoint)
	{
		$region = new DataCenterRegion;
		$region->setName($name);
		$region->setColor($color);
		$region->setEndpoint($endpoint);
		return $region;
	}
} 