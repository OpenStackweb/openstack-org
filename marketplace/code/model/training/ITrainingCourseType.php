<?php
/**
 * Interface ITrainingCourseType
 */
interface ITrainingCourseType extends IEntity {
	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @param string $type
	 * @return void
	 */
	public function setType($type);
} 