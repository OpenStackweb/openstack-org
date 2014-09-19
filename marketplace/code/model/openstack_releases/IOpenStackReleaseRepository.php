<?php

/**
 * Interface IOpenStackReleaseRepository
 */
interface IOpenStackReleaseRepository extends IEntityRepository{
	/**
	 * @param string $name
	 * @return IOpenStackRelease
	 */
	public function getByName($name);

	/**
	 * @param string $release_number
	 * @return IOpenStackRelease
	 */
	public function getByReleaseNumber($release_number);
} 