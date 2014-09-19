<?php
/**
 * Class CloudAssembler
 */
final class CloudAssembler {

	public static function convertCloudToArray(ICloudService $cloud){
		$res = OpenStackImplementationAssembler::convertOpenStackImplementationToArray($cloud);
		//override capabilities
		$res['capabilities'] = array();
		foreach($cloud->getCapabilities() as $service){
			$service_res =  OpenStackImplementationAssembler::convertCapabilityToArray($service);
			$service_res['pricing_schemas'] = array();
			foreach($service->getPricingSchemas() as $ps){
				array_push($service_res['pricing_schemas'], $ps->getIdentifier());
			}
			array_push($res['capabilities'],$service_res);
		}

		$data_centers = array();
		$locations    = array();
		$regions      = array();

		foreach($cloud->getDataCenterRegions() as $region){
			array_push($regions, CloudAssembler::convertDataCenterRegionToArray($region));
		}

		foreach($cloud->getDataCentersLocations() as $location){
			array_push($locations,CloudAssembler::convertDataCenterLocationToArray($location));
		}

		$data_centers['regions']   = $regions;
		$data_centers['locations'] = $locations;
		$res['data_centers']       = $data_centers;
		return $res;
	}

	public static function convertDataCenterRegionToArray(IDataCenterRegion $region){
		$res = array();
		$res['name']     = $region->getName();
		$res['color']    = $region->getColor();
		$res['endpoint'] = $region->getEndpoint();
		return $res;
	}

	public static function convertDataCenterLocationToArray(IDataCenterLocation $location){
		$res = array();
		$res['city']    = $location->getCity();
		$res['state']   = $location->getState();
		$res['country'] = $location->getCountry();
		$res['region']  = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $location->getDataCenterRegion()->getName()));
		$res['availability_zones'] = array();
		foreach($location->getAvailabilityZones() as $az){
			array_push($res['availability_zones'], CloudAssembler::convertAZtoArray($az));
		}
		return $res;
	}

	public static function convertAZtoArray(IAvailabilityZone $az){
		$res = array();
		$res['name'] = $az->getName();
		return $res;
	}
}