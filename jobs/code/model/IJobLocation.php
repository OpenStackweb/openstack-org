<?php

/**
 * Interface IJobLocation
 */
interface IJobLocation extends IEntity {
	/**
	 * @return string
	 */
	public function state();
	/**
	 * @return string
	 */
	public function city();
	/**
	 * @return string
	 */
	public function country();
} 