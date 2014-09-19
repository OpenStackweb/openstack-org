<?php
/**
 * Class SapphireGeoCodingQueryRepository
 */
final class SapphireGeoCodingQueryRepository
extends SapphireRepository
implements IGeoCodingQueryRepository {

	public function __construct(){
		parent::__construct(new GeoCodingQuery);
	}

	/**
	 * @param string $query
	 * @return GeoCodingQueryResult
	 */
	public function getByGeoQuery($query) {
		$qo = new QueryObject;
		$qo->addAddCondition(QueryCriteria::equal('Query',$query));
		$res = GeoCodingQuery::get()->where((string)$qo)->first();
		if(!$res) return false;
		return new GeoCodingQueryResult((float)$res->Lat, (float)$res->Lng);
	}
}