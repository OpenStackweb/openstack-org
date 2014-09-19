<?php

/**
 * Class OpenStackApiManager
 */
class OpenStackApiManager {

	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;

	/**
	 * @var IOpenStackReleaseRepository
	 */
	private $release_repository;
	/**
	 * @var IOpenStackComponentRepository
	 */
	private $component_repository;
	/**
	 * @var IOpenStackApiVersionRepository
	 */
	private $version_repository;


	/**
	 * @param IOpenStackReleaseRepository     $release_repository
	 * @param IOpenStackComponentRepository   $component_repository
	 * @param IOpenStackApiVersionRepository  $version_repository
	 * @param ITransactionManager             $tx_manager
	 */
	public function __construct(IOpenStackReleaseRepository  $release_repository,
	                            IOpenStackComponentRepository $component_repository,
	                            IOpenStackApiVersionRepository $version_repository,
	                            ITransactionManager $tx_manager){

		$this->release_repository   = $release_repository;
		$this->component_repository = $component_repository;
		$this->version_repository   = $version_repository;
		$this->tx_manager           = $tx_manager;
	}


	/**
	 * @param IOpenStackRelease $release
	 * @return bool|int
	 */
	public function registerRelease(IOpenStackRelease $release){
		$res = false;
		$release_repository = $this->release_repository;
		$this->tx_manager->transaction(function() use(&$res, $release, $release_repository) {
			$old_one = $release_repository->getByName($release->getName());
			if($old_one)
				throw new EntityAlreadyExistsException('OpenStackRelease',sprintf('name %s',$release->getName()));

			$old_one = $release_repository->getByReleaseNumber($release->getReleaseNumber());
			if($old_one)
				throw new EntityAlreadyExistsException('OpenStackRelease',sprintf('release_number %s',$release->getReleaseNumber()));

			$res = $release_repository->add($release);
		});
		return $res;
	}

	/**
	 * @param IOpenStackComponent $component
	 * @return bool|int
	 */
	public function registerComponent(IOpenStackComponent $component){
		$res = false;
		$component_repository = $this->component_repository;
		$this->tx_manager->transaction(function() use(&$res, $component, $component_repository) {
			$old_one = $component_repository->getByName($component->getName());
			if($old_one)
				throw new EntityAlreadyExistsException('OpenStackComponent',sprintf('name %s',$component->getName()));
			$res = $component_repository->add($component);
		});
		return $res;
	}

	/**
	 * @param IOpenStackApiVersion $version
	 * @return bool|int
	 */
	public function registerVersion(IOpenStackApiVersion $version){
		$res = false;
		$component_repository = $this->component_repository;
		$version_repository   = $this->version_repository;
		$this->tx_manager->transaction(function() use(&$res, $version, $component_repository, $version_repository) {

			$component_id  = $version->getReleaseComponent()->getIdentifier();
			$component     = $component_repository->getById($component_id);
			if(!$component)
				throw new NotFoundEntityException('OpenStackComponent',sprintf('id %s',$component_id));

			$old_one = $version_repository->getByVersionAndComponent($version->getVersion(), $component_id);
			if($old_one)
				throw new EntityAlreadyExistsException('OpenStackApiVersion',sprintf('version %s',$version->getVersion()));

			$res = $version_repository->add($version);
		});
		return $res;
	}

	public function registerComponentOnRelease($release_id, $component_id){
		$release   = $this->release_repository->getById($release_id);
		$component = $this->component_repository->getById($component_id);
		$release->addOpenStackComponent($component);
	}

	/**
	 * @param int $release_id
	 * @param int $component_id
	 * @return IOpenStackApiVersion[]
	 * @throws NotFoundEntityException
	 * @throws InvalidAggregateRootException
	 */
	public function getReleaseSupportedVersionsByComponent($release_id,$component_id){

		$release  = $this->release_repository->getById($release_id );
		if(!$release)
			throw new NotFoundEntityException('OpenStackRelease',sprintf('id %s',$release_id));
		$component = $this->component_repository->getById($component_id );
		if(!$component)
			throw new NotFoundEntityException('OpenStackComponent',sprintf('id %s',$component));

		if(!$release->supportsComponent($component->getCodeName()))
			throw new InvalidAggregateRootException('OpenStackRelease',$release_id,'OpenStackComponent',$component_id);

		$res = array();
		foreach($this->version_repository->getByReleaseAndComponent($release_id,$component_id) as $version){
			if($release->supportsApiVersion($version)){
				array_push($res,$version);
			}
		}
		return $res;
	}
}