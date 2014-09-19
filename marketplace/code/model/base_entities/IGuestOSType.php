<?php
/**
 * Interface IGuestOSType
 */
interface IGuestOSType extends IEntity  {
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