<?php
/**
 * Class CompanyServiceCanAddVideoPolicy
 */
final class CompanyServiceCanAddVideoPolicy implements ICompanyServiceCanAddVideoPolicy{

	/**
	 * @param ICompanyService       $company_service
	 * @param IMarketPlaceVideoType $video_type
	 * @return bool
	 * @throws PolicyException
	 */
	public function canAdd(ICompanyService $company_service, IMarketPlaceVideoType $video_type)
	{
		return true;
	}
}