<?php

/**
 * Class SapphireApplianceRepository
 */
final class SapphireApplianceRepository extends SapphireOpenStackImplementationRepository
{
	public function __construct(){
		parent::__construct(new Appliance());
	}

	/**
	 * @return string
	 */
	protected function getMarketPlaceTypeGroup()
	{
		return IAppliance::MarketPlaceGroupSlug;
	}
}