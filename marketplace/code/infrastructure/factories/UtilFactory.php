<?php
/**
 * Class UtilFactory
 */
final class UtilFactory implements IUtilFactory{

	/**
	 * @param string $query
	 * @param float $lat
	 * @param float $lng
	 * @return IGeoCodingQuery
	 */
	public function buildGeoCodingQuery($query, $lat, $lng)
	{
		$geo_coding_query = new GeoCodingQuery;
		$geo_coding_query->setQuery($query);
		$geo_coding_query->setCoordinates($lat,$lng);
		return $geo_coding_query;
	}
}