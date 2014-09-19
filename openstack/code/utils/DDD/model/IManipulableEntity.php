<?php

/**
 * Interface IManipulableEntity
 */
interface IManipulableEntity extends IEntity{
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
} 