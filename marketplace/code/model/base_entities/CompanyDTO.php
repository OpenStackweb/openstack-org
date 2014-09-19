<?php

/**
 * Class CompanyDTO
 */
class CompanyDTO {

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var int
	 */
	private $id;

	public function __construct($id,$name){
		$this->id   = $id;
		$this->name = $name;
	}

	public function getID(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}
}