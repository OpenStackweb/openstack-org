<?php

/**
 * Interface IAppliance
 */
interface IAppliance extends IOpenStackImplementation {
	const MarketPlaceType           = 'Appliance';
	const MarketPlaceGroupSlug      = 'marketplace-appliance-administrators';
	const MarketPlacePermissionSlug = 'MANAGE_MARKETPLACE_APPLIANCE';
}