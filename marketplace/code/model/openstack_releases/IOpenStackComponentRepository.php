<?php

/**
 * Interface IOpenStackComponentRepository
 */
interface IOpenStackComponentRepository extends IEntityRepository {
	/**
	 * @param string $name
	 * @return IOpenStackComponent
	 */
	public function getByName($name);

} 