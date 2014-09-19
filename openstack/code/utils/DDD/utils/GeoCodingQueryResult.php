<?php

/**
 * Class GeoCodingQueryResult
 */
final class GeoCodingQueryResult {

	private $lat;
	private $lng;

	/**
	 * @param $lat
	 * @param $lng
	 */
	public function __construct($lat, $lng){
		$this->lat = $lat;
		$this->lng = $lng;
	}

	/**
	 * @return float
	 */
	public function getLat(){
		return $this->lat;
	}

	/**
	 * @return float
	 */
	public function getLng(){
		return $this->lng;
	}
}