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
 * Class QueryAlias
 */
final class QueryAlias
{

    const INNER = "INNER";
    const LEFT  = "LEFT";
    const RIGHT = "RIGHT";


    private $sub_alias = array();
    /**
     * @var string
     */
    private $name;
    /**
     * @var null|string
     */
    private $field;
    /**
     * @var string
     */
    private $join_type;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $table_name;

    /**
     * @var QueryObject
     */
    private $query;

    /**
     * @param string $name
     * @param null|string $alias
     * @param null|string $field
     * @param string $join_type
     */
    private function __construct($name, $alias = null, $field = null, $join_type = QueryAlias::INNER)
    {
        $this->name      = $name;
        $this->alias     = $alias;
        $this->field     = is_null($field) ? $name . 'ID' : $field;
        $this->join_type = $join_type;
    }

    /**
     * @return null|string
     */
    public function getAlias(){
        return $this->alias;
    }

    private function __clone()
    {
    }

    /**
     * @param string $name
     * @param string $join_type
     * @return QueryAlias
     */
    public static function create($name, $join_type = QueryAlias::INNER)
    {
        $instance = new QueryAlias($name, null, null, $join_type);
        return $instance;
    }

    public function addAlias(QueryAlias $sub_alias)
    {
        array_push($this->sub_alias, $sub_alias);
        return $this;
    }

    /**
     * @param QueryObject $query
     */
    public function setQuery(QueryObject $query){
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getJoinType()
    {
        return $this->join_type;
    }

    public function hasSubAlias()
    {
        return count($this->sub_alias) > 0;
    }

    /**
     * @param string $table_name
     * @return $this
     */
    public function setTableName($table_name){
        $this->table_name = $table_name;
        return $this;
    }

    /**
     * @param string $join_type
     * @return JoinSpecification[]
     */
    public function subAlias($join_type = QueryAlias::INNER)
    {

        $specs = array();

        foreach ($this->sub_alias as $alias) {

            if($alias->getJoinType() !== $join_type) continue;

            $relation_name    = $alias->getName();

            $relationship_finder_strategy = new DataObjectRelationshipsFinderStrategy($alias, $this->table_name);
            list($base_entity, $has_one, $has_many, $has_many_many, $belongs_many_many) = $relationship_finder_strategy->find();

            $strategy = null;
            if (!is_null($has_many) && array_key_exists($relation_name, $has_many)) {
                $strategy = new HasManyJoinStrategy($this->query, $alias, $base_entity,  $has_many);
            }

            if (!is_null($has_one)  && array_key_exists($relation_name, $has_one)) {
                $strategy = new HasOneJoinStrategy($this->query, $alias, $base_entity, $has_one);
            }

            if(is_null($strategy)) throw new InvalidArgumentException('Join Strategy is null !!!');
            $specs = array_merge($specs, $strategy->build());
            $alias->setTableName($strategy->getBaseTable())->setQuery($this->query);

            if ($alias->hasSubAlias()) {
                $specs = array_merge($specs, $alias->subAlias($join_type));
            }
        }
        return $specs;
    }
}