<?php
/**
 * Interface IOpenStackApiVersionRepository
 */
interface IOpenStackApiVersionRepository extends IEntityRepository {

	/**
	 * @param int $id
	 * @param int $component_id
	 * @return IOpenStackApiVersion
	 */
	public function getByIdAndComponent($id, $component_id);

	/**
	 * @param string $version
	 * @param int $component_id
	 * @return IOpenStackApiVersion
	 */
	public function getByVersionAndComponent($version, $component_id);

	/**
	 * @param $release_id
	 * @param $component_id
	 * @return IOpenStackApiVersion[]
	 */
	public function getByReleaseAndComponent($release_id, $component_id);
}