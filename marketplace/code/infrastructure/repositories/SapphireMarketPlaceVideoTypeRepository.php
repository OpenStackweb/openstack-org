<?php
/**
 * Class SapphireMarketPlaceVideoTypeRepository
 */
class SapphireMarketPlaceVideoTypeRepository
extends SapphireRepository
implements IEntityRepository
{
	public function __construct(){
		parent::__construct(new MarketPlaceVideoType);
	}
}