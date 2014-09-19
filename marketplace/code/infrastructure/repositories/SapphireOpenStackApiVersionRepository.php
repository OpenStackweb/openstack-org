<?php
class SapphireOpenStackApiVersionRepository
extends SapphireRepository
implements  IOpenStackApiVersionRepository
{

	public function __construct(){
		parent::__construct(new OpenStackApiVersion);
	}

	/**
	 * @param int $version
	 * @param int $component_id
	 * @return IOpenStackApiVersion
	 */
	public function getByVersionAndComponent($version, $component_id)
	{
		return OpenStackApiVersion::get()->filter()->first(array('Version'=>$version,'OpenStackComponentID'=>$component_id));
	}

	/**
	 * @param int $id
	 * @param int $component_id
	 * @return IOpenStackApiVersion
	 */
	public function getByIdAndComponent($id, $component_id)
	{
		return OpenStackApiVersion::get()->filter(array('ID'=>$id,'OpenStackComponentID'=>$component_id ))->first();
	}

	/**
	 * @param $release_id
	 * @param $component_id
	 * @return IOpenStackApiVersion[]
	 */
	public function getByReleaseAndComponent($release_id, $component_id)
	{

		$ds = OpenStackReleaseSupportedApiVersion::get()->filter( array('OpenStackComponentID'=>$component_id ,'ReleaseID'=>$release_id));
		$list = array();
		if($ds){
			foreach($ds as $item){
				array_push($list, $item->ApiVersion());
			}
		}
		return $list;
	}
}