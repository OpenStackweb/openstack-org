<?php

interface IMarketplaceTypeRepository extends IEntityRepository {

	/**
	 * @param string $type
	 * @return IMarketPlaceType
	 */
	public function getByType($type);

} 