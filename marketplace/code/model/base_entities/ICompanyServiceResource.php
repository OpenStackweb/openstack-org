<?php
/**
 * Interface ICompanyServiceResource
 */
interface ICompanyServiceResource extends IEntity{

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param $name
	 * @return void
	 */
	public function setName($name);

	/**
	 * @return string
	 */
	public function getUri();

	/**
	 * @param string $uri
	 * @return void
	 */
	public function setUri($uri);

	/**
	 * @return string
	 */
	public function getOrder();

	/**
	 * @param string $order
	 * @return void
	 */
	public function setOrder($order);

	/**
	 * @return ICompanyService
	 */
	public function getOwner();

	/**
	 * @param ICompanyService $new_owner
	 * @return void
	 */
	public function setOwner(ICompanyService $new_owner);
} 