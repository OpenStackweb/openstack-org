<?php

/**
 * Interface IOpenStackRelease
 */
interface IOpenStackRelease extends IEntity {

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name);

	/**
	 * @return string
	 */
	public function getStatus();
	/**
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status);
	/**
	 * @return string
	 */
	public function getReleaseNumber();

	/**
	 * @param string $release_number
	 * @return void
	 */
	public function setReleaseNumber($release_number);

	/**
	 * @param bool $raw
	 * @return DateTime|string
	 */
	public function getReleaseDate($raw=true);

	/**
	 * @param DateTime $release_date
	 * @return void
	 */
	public function setReleaseDate(DateTime $release_date);

	/**
	 * @return string
	 */
	public function getReleaseNotesUrl();

	/**
	 * @param string $release_notes_url
	 * @return void
	 */
	public function setReleaseNotesUrl($release_notes_url);


	/**
	 * @return IOpenStackComponent[]
	 */
	public function getOpenStackComponents();

	/**
	 * @param int $component_id
	 * @return IOpenStackComponent
	 */
	public function getOpenStackComponent($component_id);

	/**
	 * @param IOpenStackComponent $new_component
	 * @return void
	 */
	public function addOpenStackComponent(IOpenStackComponent $new_component);

	/**
	 * @param IOpenStackApiVersion $version
	 * @return bool
	 */
	public function addSupportedVersion(IOpenStackApiVersion $version);

	/**
	 * @return IOpenStackApiVersion[]
	 */
	public function getSupportedApiVersions();

	/**
	 * @param string $code_name
	 * @return bool
	 */
	public function supportsComponent($code_name);

	/**
	 * @param IOpenStackApiVersion $version
	 * @return IReleaseSupportedApiVersion
	 */
	public function supportsApiVersion(IOpenStackApiVersion $version);

} 