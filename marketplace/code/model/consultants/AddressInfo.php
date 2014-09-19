<?php
/**
 * Class AddressInfo
 * @value_object
 */
final class AddressInfo {

	private $address;
	private $address1;
	private $zip_code;
	private $state;
	private $city;
	private $country;

	/**
	 * @param string $address
	 * @param string $address1
	 * @param string $zip_code
	 * @param string $state
	 * @param string $city
	 * @param string $country
	 */
	public function __construct($address,$address1,$zip_code,$state,$city,$country){
		$this->address  = $address;
		$this->address1 = $address1;
		$this->zip_code = $zip_code;
		$this->state    = $state;
		$this->city     = $city;
		$this->country  = $country;
	}

	public function getAddress(){
		return array($this->address,$this->address1);
	}

	public function getZipCode(){
		return $this->zip_code;
	}

	public function getState(){
		return $this->state;
	}

	public function getCity(){
		return $this->city;
	}

	public function getCountry(){
		return $this->country;
	}
} 