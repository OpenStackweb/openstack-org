<?php
/**
 * Class TrainingCourseRelatedProject
 * @active_record
 * @decorator
 */
class TrainingCourseRelatedProject extends DataExtension implements ICourseRelatedProject {


	private static $belongs_many_many = array(
		'TrainingCourse'=>'TrainingCourse'
	);

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->owner->getField('Name');
	}

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->owner->getField('ID');
	}
}