<?php
/**
 * Interface IConsultantServiceOfferedType
 */
interface IConsultantServiceOfferedType extends IEntity {
	/**
	 * @return string
	 */
	public function getType();
	/**
	 * @param string $type
	 * @return void
	 */
	public function setType($type);

	/**
	 * @return int
	 */
	public function getRegionID();
} 