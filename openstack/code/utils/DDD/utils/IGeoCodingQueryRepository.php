<?php
/**
 * Interface IGeoCodingQueryRepository
 */
interface IGeoCodingQueryRepository extends IEntityRepository {
	/**
	 * @param string $query
	 * @return GeoCodingQueryResult
	 */
	public function getByGeoQuery($query);
}