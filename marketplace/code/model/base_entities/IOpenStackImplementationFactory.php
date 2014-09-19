<?php

/**
 * Interface IOpenStackImplementationFactory
 */
interface IOpenStackImplementationFactory extends ICompanyServiceFactory {
	/**
	 * @param int                         $coverage_percent
	 * @param IReleaseSupportedApiVersion $release_supported_api_version
	 * @param IOpenStackImplementation    $implementation
	 * @return IOpenStackImplementationApiCoverage
	 */
	public function buildCapability($coverage_percent, IReleaseSupportedApiVersion $release_supported_api_version, IOpenStackImplementation $implementation);

}