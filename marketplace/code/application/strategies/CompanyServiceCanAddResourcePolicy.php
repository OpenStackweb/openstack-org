<?php
/**
 * Class CompanyServiceCanAddResourcePolicy
 */
final class CompanyServiceCanAddResourcePolicy implements ICompanyServiceCanAddResourcePolicy {

	/**
	 * @param ICompanyService $company_service
	 * @return bool
	 * @throw PolicyException
	 */
	public function canAdd(ICompanyService $company_service)
	{
		return true;
	}
}