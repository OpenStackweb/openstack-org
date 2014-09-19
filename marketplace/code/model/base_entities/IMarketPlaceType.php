<?php
/**
 * Interface IMarketPlaceType
 */
interface IMarketPlaceType extends IEntity {

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
	public function getSlug();

	/**
	 * @param string $slug
	 * @return void
	 */
	public function setSlug($slug);

	/**
	 * @return bool
	 */
	public function isActive();

	/**
	 * @return void
	 */
	public function activate();

	/**
	 * @return void
	 */
	public function deactivate();

	/**
	 * @return string
	 */
	public function getAdminGroupSlug();

	/**
	 * @return ISecurityGroup
	 */
	public function getAdminGroup();

	/**
	 * @param ISecurityGroup $group
	 * @return void
	 */
	public function setAdminGroup(ISecurityGroup $group);

	/**
	 * @return ISecurityGroup
	 */
	public function createSecurityGroup();
}