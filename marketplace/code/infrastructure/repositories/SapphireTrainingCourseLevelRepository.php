<?php
/**
 * Class SapphireTrainingCourseLevelRepository
 */
class SapphireTrainingCourseLevelRepository extends SapphireRepository
	implements IEntityRepository
{
	public function __construct(){
	parent::__construct(new TrainingCourseLevel);
}
} 