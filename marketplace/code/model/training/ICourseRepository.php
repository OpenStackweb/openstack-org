<?php
/**
 * Interface ICourseRepository
 */
interface ICourseRepository extends IEntityRepository {

	/**
	 * @param int    $training_id
	 * @param string $current_date
	 * @param string $topic
	 * @param string $location
	 * @param string $level
	 * @param bool   $limit
	 * @return CourseDTO[]
	 */
	public function get($training_id, $current_date,$topic="",$location="",$level="",$limit=true);

	/**
	 * @param string $current_date
	 * @param int    $limit
	 * @return CourseDTO[]
	 */
	public function getUpcoming($current_date,$limit=20);

	/**
	 * @param int $course_id
	 * @return TrainingCourseLocationDTO[]
	 */
	public function getLocations($course_id);

	/**
	 * @param int $course_id
	 * @param string $current_date
	 * @return TrainingCourseLocationDTO[]
	 */
	public function getLocationsByDate($course_id, $current_date);

} 