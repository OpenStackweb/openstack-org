<?php
/**
 * Class EventPointOfContact
 */
final class EventPointOfContact {
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $email;

	/**
	 * @param string $name
	 * @param string $email
	 */
	public function __construct($name, $email){
		$this->name  = $name;
		$this->email = $email;
	}

	public function getName(){
		return $this->name;
	}

	public function getEmail(){
		return $this->email;
	}
}