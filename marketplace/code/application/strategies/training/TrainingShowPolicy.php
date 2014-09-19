<?php

class TrainingShowPolicy implements IMarketPlaceTypeCanShowInstancePolicy{

	/**
	 * @param int $company_service_id
	 * @return bool
	 */
	public function canShow($company_service_id)
	{
		return true;
	}
}