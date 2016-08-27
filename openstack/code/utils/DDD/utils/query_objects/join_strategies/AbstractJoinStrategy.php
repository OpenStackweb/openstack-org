<?php
/**
 * Copyright 2016 OpenStack Foundation
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
 * Class AbstractJoinStrategy
 */
abstract class AbstractJoinStrategy implements IJoinStrategy
{
    /**
     * @var QueryObject
     */
    protected $query;

    /**
     * @var string
     */
    protected $base_table;

    /**
     * @var array
     */
    protected $relations;

    /**
     * @var QueryAlias
     */
    protected $alias;

    protected $base_entity;

    public function __construct(QueryObject $query, QueryAlias $alias, $base_entity, array $relations)
    {
        $this->query         = $query;
        $this->alias         = $alias;
        $this->base_entity   = $base_entity;
        $this->relations     = $relations;
    }

    /**
     * @return QueryObject
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getBaseTable()
    {
        return $this->base_table;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }
}