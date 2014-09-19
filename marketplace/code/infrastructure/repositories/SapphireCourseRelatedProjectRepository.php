<?php
/**
 * Class SapphireCourseRelatedProjectRepository
 */
class SapphireCourseRelatedProjectRepository extends SapphireRepository
	implements IEntityRepository
{
	public function __construct(){
		parent::__construct(new Project);
	}
} 