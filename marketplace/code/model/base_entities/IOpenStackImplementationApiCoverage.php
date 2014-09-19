<?php

/**
 * Interface IOpenStackImplementationApiCoverage
 */
interface IOpenStackImplementationApiCoverage extends IEntity {

	/**
	 * @return int
	 */
	public function getCoveragePercent();

	/**
	 * @param int $coverage
	 * @return void
	 */
	public function setCoveragePercent($coverage);

	/**
	 * @return IOpenStackImplementation
	 */
	public function getImplementation();

	/**
	 * @param IOpenStackImplementation $implementation
	 * @return void
	 */
	public function setImplementation(IOpenStackImplementation $implementation);

	/**
	 * @return IReleaseSupportedApiVersion
	 */
	public function getReleaseSupportedApiVersion();

	public function setReleaseSupportedApiVersion(IReleaseSupportedApiVersion $release_supported_api_version);

	/**
	 * @return bool
	 */
	public function SupportsVersioning();

} 