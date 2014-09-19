<?php

/**
 * Interface IPrivateCloudService
 */
interface IPrivateCloudService extends IOpenStackImplementation  {

	const MarketPlaceType           = 'Private Cloud';
	const MarketPlaceGroupSlug      = 'marketplace-private-cloud-administrators';
	const MarketPlacePermissionSlug = 'MANAGE_MARKETPLACE_PRIVATE_CLOUD';
} 