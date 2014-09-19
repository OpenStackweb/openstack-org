<?php
/**
 * Interface IJob
 */
interface IJob extends IEntity {
	/**
	 * @return void
	 */
	public function deactivate();

	/**
	 * @return IJobLocation[]
	 */
	public function locations();

	public function addLocation(IJobLocation $location);

	/**
	 * @return string
	 */
	public function getFormattedLocation();

	/**
	 * @return void
	 */
	public function clearLocations();
}