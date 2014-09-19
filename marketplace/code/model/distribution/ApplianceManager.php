<?php
/**
 * Class ApplianceManager
 */
final class ApplianceManager extends OpenStackImplementationManager {
	/**
	 * @return IMarketPlaceType
	 * @throws NotFoundEntityException
	 */
	protected function getMarketPlaceType()
	{
		$marketplace_type =  $this->marketplace_type_repository->getByType(IAppliance::MarketPlaceType);
		if(!$marketplace_type)
			throw new NotFoundEntityException('MarketPlaceType',sprintf("type %s ",IAppliance::MarketPlaceType));
		return $marketplace_type;
	}
} 