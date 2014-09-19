<?php

interface IRegionalSupportedCompanyServiceFactory extends ICompanyServiceFactory{
	/**
	 * @param IRegion                  $region
	 * @param IRegionalSupportedCompanyService $service
	 * @return IRegionalSupport
	 */
	public function buildRegionalSupport(IRegion $region, IRegionalSupportedCompanyService $service);
} 