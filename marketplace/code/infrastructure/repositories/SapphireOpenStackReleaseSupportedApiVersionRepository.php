<?php
/**
 * Class SapphireOpenStackReleaseSupportedApiVersionRepository
 */
final class SapphireOpenStackReleaseSupportedApiVersionRepository
	extends SapphireRepository
	implements IOpenStackReleaseSupportedApiVersionRepository
{

	public function __construct(){
		parent::__construct(new OpenStackReleaseSupportedApiVersion);
	}

	/**
	 * @param int $release_id
	 * @param int $component_id
	 * @param int $api_version_id
	 * @return IReleaseSupportedApiVersion
	 */
	public function getByReleaseAndComponentAndApiVersion($release_id, $component_id, $api_version_id)
	{
		$class = $this->entity_class;
		return $class::get()->filter(array('ReleaseID'=>$release_id,'OpenStackComponentID'=>$component_id,'ApiVersionID'=>$api_version_id))->first();
	}
}