<?php

/**
 * Interface ICloudService
 */
interface ICloudService extends IOpenStackImplementation {

	/**
	 * @return IDataCenterLocation[]
	 */
	public function getDataCentersLocations();

	/**
	 * @param IDataCenterLocation $data_center_location
	 * @throws EntityValidationException
	 * @return void
	 */
	public function addDataCenterLocation(IDataCenterLocation $data_center_location);

	/**
	 * @return void
	 */
	public function clearDataCentersLocations();

	/**
	 * @param string $region_slug
	 * @return IDataCenterRegion
	 */
	public function getDataCenterRegion($region_slug);


	/**
	 * @return IDataCenterRegion[]
	 */
	public function getDataCenterRegions();

	/**
	 * @param IDataCenterRegion $region
	 * @return void
	 */
	public function addDataCenterRegion	(IDataCenterRegion $region);

	/**
	 * @return void
	 */
	public function clearDataCenterRegions();
} 