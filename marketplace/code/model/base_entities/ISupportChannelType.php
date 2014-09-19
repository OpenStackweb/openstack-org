<?php

/**
 * Interface ISupportChannelType
 */
interface  ISupportChannelType extends IEntity {
	/**
	 * @param string $type
	 * @return void
	 */
	public function setType($type);

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return string
	 */
	public function getInfo();
} 