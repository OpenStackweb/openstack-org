<?php

/**
 * Class TrainingAddPolicy
 */
class TrainingAddPolicy implements IMarketPlaceTypeAddPolicy {


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