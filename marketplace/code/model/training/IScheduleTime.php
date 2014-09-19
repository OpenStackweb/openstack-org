<?php

/**
 * Interface IScheduleTime
 */
interface IScheduleTime extends IEntity {

	/**
	 * @return string
	 */
	public function getStartDate();

	/**
	 * @param string $start_date
	 * @return void
	 */
	public function setStartDate($start_date);

	/**
	 * @return string
	 */
	public function getEndDate();

	/**
	 * @param string $end_date
	 * @return void
	 */
	public function setEndDate($end_date);

	/**
	 * @return string
	 */
	public function getLink();

	/**
	 * @param string $link
	 * @return void
	 */
	public function setLink($link);

	/**
	 * @return ICourseLocation
	 */
	public function getAssociatedLocation();

	/**
	 * @param ICourseLocation $location
	 * @return void
	 */
	public function setAssociatedLocation(ICourseLocation $location);

} 