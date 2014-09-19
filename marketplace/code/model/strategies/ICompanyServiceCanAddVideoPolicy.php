<?php

/**
 * Interface ICompanyServiceCanAddVideoPolicy
 */
interface ICompanyServiceCanAddVideoPolicy {
	/**
	 * @param ICompanyService       $company_service
	 * @param IMarketPlaceVideoType $video_type
	 * @return bool
	 * @throws PolicyException
	 */
	public function canAdd(ICompanyService $company_service,IMarketPlaceVideoType $video_type);
} 