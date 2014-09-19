<?php
/**
 * Class ConsultantAddPolicy
 */
final class ConsultantAddPolicy implements IMarketPlaceTypeAddPolicy {

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