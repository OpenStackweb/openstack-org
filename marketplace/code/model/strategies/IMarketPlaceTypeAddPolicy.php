<?php
/**
 * Interface IMarketPlaceTypeAddPolicy
 */
interface IMarketPlaceTypeAddPolicy {

	/**
	 * @param ICompany $company
	 * @return bool
	 * @throws PolicyException
	 */
	public function canAdd(ICompany $company);
} 