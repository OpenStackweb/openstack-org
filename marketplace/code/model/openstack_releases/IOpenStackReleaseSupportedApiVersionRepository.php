<?php
/**
 * Interface IOpenStackReleaseSupportedApiVersionRepository
 */
interface IOpenStackReleaseSupportedApiVersionRepository extends IEntityRepository {
	/**
	 * @param int $release_id
	 * @param int $component_id
	 * @param int $api_version_id
	 * @return IReleaseSupportedApiVersion
	 */
	public function getByReleaseAndComponentAndApiVersion($release_id,$component_id,$api_version_id);
}