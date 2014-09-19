<?php

/**
 * Class PrivateCloudManager
 */
final class PrivateCloudManager extends CloudManager {

	/**
	 * @return IMarketPlaceType
	 * @throws NotFoundEntityException
	 */
	protected function getMarketPlaceType(){
		$marketplace_type =  $this->marketplace_type_repository->getByType(IPrivateCloudService::MarketPlaceType);
		if(!$marketplace_type)
			throw new NotFoundEntityException('MarketPlaceType',sprintf("type %s ",IPrivateCloudService::MarketPlaceType));
		return $marketplace_type;
	}
}