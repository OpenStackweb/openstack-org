<?php

interface IGeoCodingQueryCacheService {
	/**
	 * @param string $city
	 * @param string $country
	 * @param string $state
	 * @param string $address
	 * @param string $zip_code
	 * @return GeoCodingQueryResult
	 */
	public function getGeoQuery($city, $country ,$state = null, $address = null, $zip_code = null);
} 