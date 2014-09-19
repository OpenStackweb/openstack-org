<?php
/**
 * Interface ITrainingCourseLevel
 */
interface ITrainingCourseLevel extends IEntity {
	/**
	 * @param string $level
	 * @return void
	 */
	public function setLevel($level);

	/**
	 * @return string
	 */
	public function getLevel();
} 