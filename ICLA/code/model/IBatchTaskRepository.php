<?php

/**
 * Interface IBatchTaskRepository
 */
interface IBatchTaskRepository extends IEntityRepository {
	/***
	 * @param string $name
	 * @return IBatchTask
	 */
	public function findByName($name);
}