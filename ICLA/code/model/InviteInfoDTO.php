<?php

/**
 * Class InviteInfoDTO
 */
class InviteInfoDTO {

	/**
	 * @var string
	 */
	protected  $first_name;

	/**
	 * @var string
	 */
	protected $last_name;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $email
	 */
	public function __construct($first_name, $last_name, $email){
		$this->first_name = $first_name;
		$this->last_name  = $last_name;
		$this->email      = $email;
	}

	/**
	 * @return string
	 */
	public function getFirstName(){
		return $this->first_name;
	}

	/**
	 * @return string
	 */
	public function getLastName(){
		return $this->last_name;
	}

	/**
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}
} 