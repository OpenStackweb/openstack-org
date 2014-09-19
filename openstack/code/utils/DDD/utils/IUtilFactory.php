<?php
/***
 * Interface IUtilFactory
 */
interface IUtilFactory {
	/**
	 * @param string $query
	 * @param float $lat
	 * @param float $lng
	 * @return IGeoCodingQuery
	 */
	public function buildGeoCodingQuery($query, $lat, $lng);
} 