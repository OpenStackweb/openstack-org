<?php

/**
 * Interface IHyperVisorType
 */
interface IHyperVisorType extends IEntity {

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @param string $type
	 * @return void
	 */
	public function setType($type);
} 