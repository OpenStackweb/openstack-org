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
 * Class QueryObject
 */
final class QueryObject
{

    private $and_conditions = array();
    private $or_conditions = array();
    private $order_conditions = array();
    private $alias = array();
    /**
     * @var IEntity
     */
    private $base_entity;

    /**
     * QueryObject constructor.
     * @param IEntity|null $base_entity
     */
    public function __construct(IEntity $base_entity = null)
    {
        $this->base_entity = $base_entity;
    }

    /**
     * @param IEntity $base_entity
     */
    public function setBaseEntity(IEntity $base_entity)
    {
        $this->base_entity = $base_entity;
    }

    /**
     * @return IEntity
     */
    public function getBaseEntity(){
        return $this->base_entity;
    }

    /**
     * @param QueryCriteria $condition
     * @return QueryObject
     */
    public function addOrCondition(QueryCriteria $condition)
    {
        if (!is_null($condition))
            array_push($this->or_conditions, $condition);
        return $this;
    }

    /**
     * @param QueryCriteria $condition
     * @return QueryObject
     */
    public function addAndCondition(QueryCriteria $condition)
    {
        if (!is_null($condition))
            array_push($this->and_conditions, $condition);
        return $this;
    }

    /**
     * @param QueryOrder $order
     * @return QueryObject
     */
    public function addOrder(QueryOrder $order)
    {
        if (!is_null($order))
            array_push($this->order_conditions, $order);
        return $this;
    }


    public function addAlias(QueryAlias $alias)
    {
        if (!is_null($alias))
            array_push($this->alias, $alias);
        return $this;
    }

    public function getOrder()
    {
        $res = array();
        foreach ($this->order_conditions as $condition) {
            $res[$condition->getField()] = $condition->getDir();
        }
        return $res;
    }

    /**
     * @param string $join_type
     * @return JoinSpecification[]
     */
    public function getAlias($join_type = QueryAlias::INNER)
    {
        $specs = array();

        foreach ($this->alias as $alias)
        {
            if($alias->getJoinType() !== $join_type) continue;

            $relation_name     = $alias->getName();

            // get entity relationships...
            $strategy          =  null;

            $relationship_finder_strategy = new DataObjectRelationshipsFinderStrategy($alias, get_class($this->base_entity));
            list($base_entity, $has_one, $has_many, $has_many_many, $belongs_many_many) = $relationship_finder_strategy->find();

            // has many spec
            if (!is_null($has_many) && array_key_exists($relation_name, $has_many)) {
               $strategy  = new HasManyJoinStrategy($this, $alias, $base_entity , $has_many);
            }

            // has one spec
            if (!is_null($has_one)  && array_key_exists($relation_name, $has_one) ) {
                $strategy = new HasOneJoinStrategy($this, $alias, $base_entity, $has_one);
            }

            // has many many spec
            if (!is_null($has_many_many) && array_key_exists($relation_name, $has_many_many)) {
                $strategy = new HasManyManyJoinStrategy($this, $alias, $base_entity, $has_many_many);
            }

            // belongs many many spec
            if (!is_null($belongs_many_many) && array_key_exists($relation_name, $belongs_many_many)  ) {
               $strategy = new BelongsManyManyJoinStrategy($this, $alias, $base_entity, $belongs_many_many);
            }

            if(is_null($strategy))
                throw new InvalidArgumentException('Join Strategy is null !!!');

            $specs = array_merge($specs, $strategy->build());
            $alias->setTableName($strategy->getBaseTable())->setQuery($this);

            if ($alias->hasSubAlias()) {
                $specs = array_merge($specs, $alias->subAlias($join_type));
            }
        }
        return $specs;
    }

    /**
     * Clear conditions
     */
    public function clear()
    {
        $this->and_conditions = array();
        $this->or_conditions = array();
        $this->order_conditions = array();
        $this->alias = array();
    }

    public function __toString()
    {
        $query = '';

        foreach ($this->and_conditions as $condition) {
            $condition->setBaseEntity($this->base_entity);
            if (!empty($query))
                $query .= 'AND';
            $query .= (string)$condition;
        }

        foreach ($this->or_conditions as $condition) {
            $condition->setBaseEntity($this->base_entity);
            if (!empty($query))
                $query .= 'OR';
            $query .= (string)$condition;
        }
        return $query;
    }
} 