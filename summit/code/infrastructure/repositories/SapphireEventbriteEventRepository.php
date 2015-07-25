<?php

/**
 * Copyright 2015 OpenStack Foundation
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
final class SapphireEventbriteEventRepository extends SapphireRepository  implements IEventbriteEventRepository
{

    public function __construct()
    {
        parent::__construct(new EventbriteEvent);
    }

    /**
     * @param int $offset
     * @param int $count
     * @return array
     */
    public function getUnprocessed($offset, $count)
    {
        $query = new QueryObject(new EventbriteEvent);
        $query->addAndCondition(QueryCriteria::equal('Processed', 0));
        $query->addOrder(QueryOrder::asc('ID'));
        return $this->getAll($query, $offset, $count);
    }

    /**
     * @param string $url
     * @return IEventbriteEvent
     */
    public function getByApiUrl($url)
    {
        $query = new QueryObject(new EventbriteEvent);
        $query->addAndCondition(QueryCriteria::equal('ApiUrl', $url));
        return $this->getBy($query);
    }
}