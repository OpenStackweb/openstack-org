<?php
class SapphireOpenStackReleaseRepository
	extends SapphireRepository
implements  IOpenStackReleaseRepository
{

	public function __construct(){
		parent::__construct(new OpenStackRelease);
	}

	/**
	 * @param IEntity $entity
	 * @return int
	 */
	public function add(IEntity $entity)
	{
		//supported components
		foreach($entity->getOpenStackComponents(true) as $component){
			$entity->getManyManyComponents('Components')->Add($component);
		}
		//supported versions
		foreach($entity->getSupportedApiVersions(true) as $supported_version){
			$entity->getComponents('SupportedApiVersions')->add($supported_version);
		}

		return $entity->write();
	}

	/**
	 * @param string $name
	 * @return IOpenStackRelease
	 */
	public function getByName($name)
	{
		$class = $this->entity_class;
		return $class::get()->filter('Name',$name)->first();
	}

	/**
	 * @param string $release_number
	 * @return IOpenStackRelease
	 */
	public function getByReleaseNumber($release_number)
	{
		$class = $this->entity_class;
		return $class::get()->filter('ReleaseNumber', $release_number)->first();
	}
}