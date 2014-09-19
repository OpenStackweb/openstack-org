<?php

/**
 * Class SapphirePrivateCloudRepository
 */
final class SapphirePrivateCloudRepository
	extends SapphireOpenStackImplementationRepository {

	public function __construct(){
		parent::__construct(new PrivateCloudService);
	}

	public function delete(IEntity $entity){

		parent::delete($entity);
	}

	/**
	 * @return string
	 */
	protected function getMarketPlaceTypeGroup()
	{
		return IPrivateCloudService::MarketPlaceGroupSlug;
	}
} 