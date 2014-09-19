<?php
/**
 * Class PublicCloudManager
 */
final class PublicCloudManager extends CloudManager {

	/**
	 * @return IMarketPlaceType
	 * @throws NotFoundEntityException
	 */
	protected function getMarketPlaceType()	{
		$marketplace_type =  $this->marketplace_type_repository->getByType(IPublicCloudService::MarketPlaceType);
		if(!$marketplace_type)
			throw new NotFoundEntityException('MarketPlaceType',sprintf("type %s ",IPublicCloudService::MarketPlaceType));
		return $marketplace_type;
	}

} 