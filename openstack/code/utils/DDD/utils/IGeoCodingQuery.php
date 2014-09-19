<?php
/**
 * Interface IGeoCodingQuery
 */
interface IGeoCodingQuery extends IEntity {
	/**
	 * @param string $query
	 * @return void
	 */
	public function setQuery($query);

	/**
	 * @param float $lat
	 * @param float $lng
	 * @return void
	 */
	public function setCoordinates($lat,$lng);
} 