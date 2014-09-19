<?php

/**
 * Class JobLocationDTO
 */
final class JobLocationDTO {

	/**
	 * @var string
	 */
	private $country;

	/**
	 * @var string
	 */
	private $city;

	/**
	 * @var string
	 */
	private $state;

	/**
	 * @param string $city
	 * @param string $country
	 * @param null|string $state
	 */
	public function __construct($city, $country, $state=null){
		$this->city = $city;
		$this->country = $country;
		$this->state = $state;
	}

	public function getCity(){
		return $this->city;
	}

	public function getCountry(){
		return $this->country;
	}

	public function getState(){
		return $this->state;
	}
} 