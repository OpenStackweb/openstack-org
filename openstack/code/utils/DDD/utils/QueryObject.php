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
    private $base_entity;

    public function __construct(IEntity $base_entity = null)
    {
        $this->base_entity = $base_entity;
    }

    public function setBaseEntity(IEntity $base_entity)
    {
        $this->base_entity = $base_entity;
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

    public function getAlias($join_type = QueryAlias::INNER)
    {
        $join = array();

        foreach ($this->alias as $alias) {
            if($alias->getJoinType() !== $join_type) continue;

            $child      = $alias->getName();
            $has_many   = Config::inst()->get(get_class($this->base_entity), 'has_many');
            $class_name = ClassInfo::baseDataClass($this->base_entity);

            if (!is_null($has_many)) {
                $has_many_classes = array_flip($has_many);
                if (array_key_exists($child, $has_many_classes)) {
                    $tableClasses = ClassInfo::dataClassesFor($child);
                    $baseClass = array_shift($tableClasses);
                    $joinField = $this->base_entity->getRemoteJoinField($has_many_classes[$child], 'has_many');
                    $join[$baseClass] = $baseClass . '.' . $joinField . ' = ' . $class_name . '.ID';
                    $this->addAndCondition(QueryCriteria::equal("{$baseClass}.ClassName", $child));
                }
            }


            $has_many_many = Config::inst()->get(get_class($this->base_entity), 'many_many');
            if (!is_null($has_many_many)) {
                $has_many_many_classes = array_flip($has_many_many);
                if (array_key_exists($child, $has_many_many_classes)) {
                    $base_entity_name = get_class($this->base_entity);
                    $component = $has_many_many_classes[$child];
                    $joinTable = "{$base_entity_name}_{$component}";
                    $parentField = $base_entity_name . "ID";
                    $childField = $child . "ID";
                    $join[$joinTable] = $joinTable . '.' . $parentField . ' = ' . $class_name . '.ID';
                    $join[$child] = $child . '.ID = ' . $joinTable . '.' . $childField;
                }
            }


            $has_one = Config::inst()->get(get_class($this->base_entity), 'has_one');
            if (!is_null($has_one)) {
                if (array_key_exists($child, $has_one)) {
                    $table = $has_one[$child];
                    $join[$table] = $has_one[$child] . '.ID = ' . $class_name . '.' . $child . 'ID';
                } else {
                    $has_one_classes = array_flip($has_one);
                    if (array_key_exists($child, $has_one_classes)) {

                        $join[$child] = $child . '.ID = ' . $class_name . '.' . $has_one_classes[$child] . 'ID';
                    }
                }
            }

            $belongs_many_many = Config::inst()->get(get_class($this->base_entity), 'belongs_many_many');
            if (!is_null($belongs_many_many)) {
                $belongs_many_many_classes = array_flip($belongs_many_many);
                if (array_key_exists($child, $belongs_many_many_classes)) {
                    $child_many_many = Config::inst()->get($child, 'many_many');
                    $child_many_many_classes = array_flip($child_many_many);
                    $component_name = $child_many_many_classes[$class_name];
                    list($parentClass, $componentClass, $child_join_field, $join_field, $join_table) = Singleton($child)->many_many($component_name);
                    $join[$join_table] = $join_table . '`.' . $join_field . ' = `' . $class_name . '`.ID';
                    $join[$child] = $child . '`.ID = `' . $join_table . '`.' . $child_join_field;
                }
            }
            if ($alias->hasSubAlias()) {
                $join = array_merge($join, $alias->subAlias($join_type));
            }
        }
        return $join;
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