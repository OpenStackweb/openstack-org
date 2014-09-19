<?php
/**
 * Interface IConsultantClient
 */
interface IConsultantClient extends IEntity {
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
	 * @return void
	 */
	public function getOrder();

	/**
	 * @param int $order
	 * @return void
	 */
	public function setOrder($order);


	/**
	 * @return IConsultant
	 */
	public function getConsultant();

	/**
	 * @param IConsultant $consultant
	 * @return void
	 */
	public function setConsultant(IConsultant $consultant);
} 