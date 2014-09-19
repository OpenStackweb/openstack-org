<?php

/**
 * Interface IReleaseSupportedApiVersion
 */
interface IReleaseSupportedApiVersion extends IEntity {

	/**
	 * @param IOpenStackComponent $component
	 * @return void
	 */
	public function setOpenStackComponent(IOpenStackComponent $component);

	/**
	 * @return IOpenStackComponent
	 */
	public function getOpenStackComponent();

	/**
	 * @param IOpenStackApiVersion $version
	 * @return void
	 */
	public function setApiVersion(IOpenStackApiVersion $version);

	/**
	 * @return IOpenStackApiVersion
	 */
	public function getApiVersion();

	/**
	 * @param IOpenStackRelease $release
	 * @return void
	 */
	public function setRelease(IOpenStackRelease $release);

	/**
	 * @return IOpenStackRelease
	 */
	public function getRelease();
} 