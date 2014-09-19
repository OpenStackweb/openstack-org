<?php
interface IRegion extends IEntity {
	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name);

	/**
	 * @return string
	 */
	public function getName();

} 