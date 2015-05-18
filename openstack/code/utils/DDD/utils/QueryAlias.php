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
    private $name;
    private $field;
    private $join_type;

    /**
     * @param string $name
     * @param null $field
     * @param string $join_type
     */
    private function __construct($name, $field = null, $join_type = QueryAlias::INNER)
    {
        $this->name = $name;
        $this->field = is_null($field) ? $name . 'ID' : $field;
        $this->join_type = $join_type;
    }

    private function __clone()
    {
    }

    /**
     * @param string $name
     * @param null $field
     * @param string $join_type
     * @return QueryAlias
     */
    public static function create($name, $field = null, $join_type = QueryAlias::INNER)
    {
        $instance = new QueryAlias($name, $field, $join_type);
        return $instance;
    }

    public function addAlias(QueryAlias $sub_alias)
    {
        array_push($this->sub_alias, $sub_alias);
        return $this;
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

    public function subAlias($join_type = QueryAlias::INNER)
    {

        $join = array();

        foreach ($this->sub_alias as $alias) {

            if($alias->getJoinType() !== $join_type) continue;

            $class_name  = ClassInfo::baseDataClass($this->name);
            $base_entity = singleton($this->name);
            $child       = $alias->getName();

            $has_many = Config::inst()->get($class_name, 'has_many');
            if (!is_null($has_many)) {
                $has_many_classes = array_flip($has_many);

                if (array_key_exists($child, $has_many_classes)) {
                    $joinField = $base_entity->getRemoteJoinField($has_many_classes[$child], 'has_many');
                    $join[$child] = $child . '.' . $joinField . ' = ' . $class_name . '.ID';
                }
            }


            $has_one = Config::inst()->get($class_name, 'has_one');
            if (!is_null($has_one)) {
                $has_one_classes = array_flip($has_one);
                if (array_key_exists($child, $has_one_classes)) {
                    $join[$child] = $child . '.ID = ' . $class_name . '.' . $has_one_classes[$child] . 'ID';
                }
            }

            if ($alias->hasSubAlias()) {
                $join = array_merge($join, $alias->subAlias($join_type));
            }
        }
        return $join;
    }
}