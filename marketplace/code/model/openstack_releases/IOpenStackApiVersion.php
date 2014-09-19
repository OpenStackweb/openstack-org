<?php
/**
 * Interface IOpenStackApiVersion
 */
interface IOpenStackApiVersion extends IEntity {
	/**
	 * @param string $version
	 * @return void
	 */
	public function setVersion($version);

	/**
	 * @return string
	 */
	public function getVersion();


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
	 * @return IOpenStackComponent
	 */
	public function getReleaseComponent();

	/**
	 * @param IOpenStackComponent $new_component
	 * @return void
	 */
	public function setReleaseComponent(IOpenStackComponent $new_component);

} 