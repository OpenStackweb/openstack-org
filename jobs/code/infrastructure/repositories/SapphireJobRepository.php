<?php
/**
 * Class SapphireJobRepository
 */
final class SapphireJobRepository extends SapphireRepository {

	public function __construct(){
		parent::__construct(new JobPage);
	}

	public function delete(IEntity $entity){
		$entity->clearLocations();
		parent::delete($entity);
	}
} 