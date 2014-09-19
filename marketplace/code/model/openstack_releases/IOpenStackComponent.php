<?php

/**
 * Interface IOpenStackComponent
 */
interface IOpenStackComponent extends IEntity {

	public function getName();
	public function setName($name);

	public function getCodeName();
	public function setCodeName($codename);

	public function getDescription();
	public function setDescription($description);

	/**
	 * @return bool
	 */
	public function getSupportsVersioning();

	/**
	 * @param bool $supports_versioning
	 * @return void
	 */
	public function setSupportsVersioning($supports_versioning);

	/**
	 * @return bool
	 */
	public function getSupportsExtensions();

	/**
	 * @param bool $supports_extensions
	 * @return void
	 */
	public function setSupportsExtensions($supports_extensions);

	/**
	 * @return IOpenStackApiVersion[]
	 */
	public function getVersions();

	/**
	 * @param IOpenStackApiVersion $new_version
	 * @return void
	 */
	public function addVersion(IOpenStackApiVersion $new_version);

	public function clearVersions();

	/**
	 * @param int $version_id
	 * @return bool
	 */
	public function hasVersion($version_id);

	/**
	 * @return IOpenStackRelease[]
	 */
	public function getSupportedReleases();

} 