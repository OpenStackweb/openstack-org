<?php
/**
 * Class SapphirePublicCloudRepository
 */
class SapphirePublicCloudRepository
	extends SapphireOpenStackImplementationRepository {
	public function __construct(){
		parent::__construct(new PublicCloudService);
	}

	public function delete(IEntity $entity){
		$entity->clearDataCenterRegions();
		$entity->clearDataCentersLocations();
		parent::delete($entity);
	}

	/**
	 * @return string
	 */
	protected function getMarketPlaceTypeGroup()
	{
		return IPublicCloudService::MarketPlaceGroupSlug;
	}
}