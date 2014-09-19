<?php

/**
 * Interface ITrainingCoursePrerequisite
 */
interface ITrainingCoursePrerequisite
extends IEntity
{
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name);
} 