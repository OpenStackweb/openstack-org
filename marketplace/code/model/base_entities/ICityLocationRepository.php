<?php
/**
 * Interface ICityLocationRepository
 */
interface ICityLocationRepository extends IEntityRepository {
	/**
	 * @param string $city
	 * @param string $country
	 * @return ICityLocation
	 */
	public function getByCityAndCountry($city,$country);
} 