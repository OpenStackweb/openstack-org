<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
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
		$qo->addAndCondition(QueryCriteria::equal('Query',$query));
		$res = GeoCodingQuery::get()->where((string)$qo)->first();
		if(!$res) return false;
		return new GeoCodingQueryResult((float)$res->Lat, (float)$res->Lng);
	}
}