<?php
/**
 * Interface IGeoCodingService
 */
interface IGeoCodingService {
	/**
	 * given a city name and an ISO 3166-1 country code
	 * return GPS coordinates array($lat,$lng)
	 * @param string $city
	 * @param string $country
	 * @param string $state
	 * @throws EntityValidationException
	 * @return array
	 */
	public function getCityCoordinates($city,$country,$state = null);

	/**
	 * given an address info
	 * return GPS coordinates array($lat,$lng)
	 * @param AddressInfo $address_info
	 * @throws EntityValidationException
	 * @return array
	 */
	public function getAddressCoordinates(AddressInfo $address_info);
} 