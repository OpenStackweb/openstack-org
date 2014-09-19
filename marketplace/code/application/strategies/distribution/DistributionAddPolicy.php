<?php

/**
 * Class DistributionAddPolicy
 */
final class DistributionAddPolicy implements IMarketPlaceTypeAddPolicy {

	/**
	 * @param ICompany $company
	 * @return bool
	 * @throws PolicyException
	 */
	public function canAdd(ICompany $company)
	{
		return true;
	}
}