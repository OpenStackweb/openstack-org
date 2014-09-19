<?php

/**
 * Interface IDistribution
 */
interface IDistribution extends IOpenStackImplementation {
	const MarketPlaceType           = 'Distribution';
	const MarketPlaceGroupSlug      = 'marketplace-distribution-administrators';
	const MarketPlacePermissionSlug = 'MANAGE_MARKETPLACE_DISTRIBUTION';
}