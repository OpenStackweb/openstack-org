<?php

/**
 * Class SapphireDistributionRepository
 */
class SapphireDistributionRepository
extends SapphireOpenStackImplementationRepository

{
	public function __construct(){
		parent::__construct(new Distribution);
	}

	/**
	 * @return string
	 */
	protected function getMarketPlaceTypeGroup()
	{
		return IDistribution::MarketPlaceGroupSlug;
	}
}