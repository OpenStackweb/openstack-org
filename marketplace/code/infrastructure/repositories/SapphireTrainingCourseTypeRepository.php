<?php

/**
 * Class SapphireTrainingCourseTypeRepository
 */
class SapphireTrainingCourseTypeRepository
	extends SapphireRepository
	implements IEntityRepository
{
	public function __construct(){
		parent::__construct(new TrainingCourseType);
	}
} 