<?php

interface IMarketPlaceTypeCanShowInstancePolicy {
	/**
	 * @param int $company_service_id
	 * @return bool
	 * @throws PolicyException
	 */
	public function canShow($company_service_id);
} 