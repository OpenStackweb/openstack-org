<?php
/**
 * Class DistributionManager
 */
final class DistributionManager extends OpenStackImplementationManager {

	/**
	 * @return IMarketPlaceType
	 * @throws NotFoundEntityException
	 */
	protected function getMarketPlaceType()
	{
		$marketplace_type =  $this->marketplace_type_repository->getByType(IDistribution::MarketPlaceType);
		if(!$marketplace_type)
			throw new NotFoundEntityException('MarketPlaceType',sprintf("type %s ",IDistribution::MarketPlaceType));
		return $marketplace_type;
	}
} 