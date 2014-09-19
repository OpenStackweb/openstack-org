<?php
/**
 * Interface ISpokenLanguage
 */
interface ISpokenLanguage extends IEntity{
	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name);
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return int
	 */
	public function getOrder();

} 