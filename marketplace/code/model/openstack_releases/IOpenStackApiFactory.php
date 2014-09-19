<?php

/**
 * Interface IOpenStackApiFactory
 */
interface IOpenStackApiFactory {


	/**
	 * @param string $name
	 * @param string $release_number
	 * @param DateTime $release_date
	 * @param string $release_notes_url
	 * @return IOpenStackRelease
	 */
	public function buildOpenStackRelease($name, $release_number, DateTime $release_date, $release_notes_url);


	/**
	 * @param int $id
	 * @return IOpenStackRelease
	 */
	public function buildOpenStackReleaseById($id);

	/**
	 * @param string $name
	 * @param string $code_name
	 * @param string $description
	 * @return IOpenStackComponent
	 */
	public function buildOpenStackComponent($name, $code_name, $description);

	/**
	 * @param int $id
	 * @return IOpenStackComponent
	 */
	public function buildOpenStackComponentById($id);

	/***
	 * @param string           $version
	 * @param string           $status
	 * @param IOpenStackComponent $component
	 * @return IOpenStackApiVersion
	 */
	public function buildOpenStackApiVersion($version,$status, IOpenStackComponent $component);

	/**
	 * @param int $id
	 * @return IOpenStackApiVersion
	 */
	public function buildOpenStackApiVersionById($id);

	/**
	 * @param IOpenStackRelease    $release
	 * @param IOpenStackComponent  $component
	 * @param IOpenStackApiVersion $api_version
	 * @return IReleaseSupportedApiVersion
	 */
	public function buildReleaseSupportedApiVersion(IOpenStackRelease $release,IOpenStackComponent $component,IOpenStackApiVersion $api_version );
} 