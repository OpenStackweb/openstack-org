<?php

abstract class OpenStackImplementationFactory
	extends RegionalSupportedCompanyServiceFactory
	implements IOpenStackImplementationFactory {
	/**
	 * @param int                      $coverage_percent
	 * @param IReleaseSupportedApiVersion $release_supported_api_version
	 * @param IOpenStackImplementation $implementation
	 * @return IOpenStackImplementationApiCoverage
	 */
	public function buildCapability($coverage_percent, IReleaseSupportedApiVersion $release_supported_api_version, IOpenStackImplementation $implementation)
	{
		$capability = new OpenStackImplementationApiCoverage;
		$capability->setCoveragePercent($coverage_percent);
		$capability->setReleaseSupportedApiVersion($release_supported_api_version);
		$capability->setImplementation($implementation);
		return $capability;
	}


} 