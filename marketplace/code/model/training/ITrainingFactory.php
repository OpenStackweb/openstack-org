<?php
interface ITrainingFactory extends ICompanyServiceFactory {

	/**
	 * @param string  $name
	 * @param string $description
	 * @param bool $active
	 * @param ICompany $company
	 * @return ITraining|TrainingService
	 */
	public function buildTraining($name,$description,$active, ICompany $company);

	/**
	 * @param string $city
	 * @param string $state
	 * @param string $country
	 * @return ICourseLocation
	 */
	public function buildCourseLocation($city, $state, $country);

	/**
	 * @param string $start_date
	 * @param string $end_date
	 * @param string $link
	 * @return IScheduleTime
	 */
	public function buildCourseScheduleTime($start_date, $end_date, $link);

	/**
	 * @param int $project_id
	 * @return ICourseRelatedProject
	 */
	public function buildCourseRelatedProject($project_id);

	/**
	 * @return ICourse
	 */
	public function buildCourse();

} 