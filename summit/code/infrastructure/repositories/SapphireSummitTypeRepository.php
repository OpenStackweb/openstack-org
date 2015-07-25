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
 * Class SapphireSummitTypeRepository
 */
final class SapphireSummitTypeRepository extends SapphireRepository {

	public function __construct(){
		parent::__construct(new SummitType);
	}

    public function getLastIdInserted($summit_id) {
        $query = new QueryObject(new SummitType());
        $query->addAndCondition(QueryCriteria::equal('SummitID',$summit_id));
        $query->addOrder(QueryOrder::desc('ID'));
        list($list,$count) = $this->getAll($query,0,1);
        return array_pop($list)->ID;
    }

}