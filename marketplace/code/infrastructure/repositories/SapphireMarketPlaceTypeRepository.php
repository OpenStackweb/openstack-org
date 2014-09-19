<?php

/**
 * Class SapphireMarketPlaceTypeRepository
 */
class SapphireMarketPlaceTypeRepository
	extends SapphireRepository
	implements IMarketplaceTypeRepository {

	public function __construct(){
		parent::__construct(new MarketPlaceType);
	}
	/**
	 * @param string $type
	 * @return IMarketPlaceType
	 */
	public function getByType($type)
	{
		return MarketPlaceType::get()->filter('Name',$type)->first();
	}
}