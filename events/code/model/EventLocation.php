<?php
/**
 * Class EventLocation
 */
final class EventLocation {
	/**
	 * @var string
	 */
	private $city;
	/**
	 * @var string
	 */
	private $state;
	/**
	 * @var string
	 */
	private $country;

	private $lat;

	private $lng;

	/**
	 * @param string $city
	 * @param string $state
	 * @param string $country
	 */
	public function __construct($city,$state,$country){
		$this->city    = $city;
		$this->state   = $state;
		$this->country = $country;
	}

	/**
	 * @return string
	 */
	public function getCity(){
		return $this->city;
	}

	/**
	 * @return string
	 */
	public function getState(){
		return $this->state;
	}

	/**
	 * @return string
	 */
	public function getCountry(){
		return $this->country;
	}

	/**
	 * @param float $lat
	 * @param float $lng
	 */
	public function setCoordinates($lat, $lng){
		$this->lat = $lat;
		$this->lng = $lng;
	}

	/**
	 * @return array
	 */
	public function getCoordinates(){
		return array($this->lat,$this->lng);
	}
} 