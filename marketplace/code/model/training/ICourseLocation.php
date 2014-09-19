<?php
/**
 * Interface ICourseLocation+
 */
interface ICourseLocation extends IEntity {

	/**
	 * @return string
	 */
	public function getCountry();

	/**
	 * @param string $country
	 * @return void
	 */
	public function setCountry($country);

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
	 * @return string
	 */
	public function getCity();

	/**
	 * @param string $city
	 * @return void
	 */
	public function setCity($city);

	/**
	 * @return ICourse
	 */
	public function getAssociatedCourse();

	/**
	 * @param ICourse $course
	 * @return void
	 */
	public function setAssociatedCourse(ICourse $course);

	/**
	 * @return IScheduleTime[]
	 */
	public function getDates();

	/**
	 * @param IScheduleTime $date
	 * @return void
	 */
	public function addDate(IScheduleTime $date);

	/**
	 * @return void
	 */
	public function clearDates();

} 