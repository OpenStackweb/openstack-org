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
 * Class JoinSpecification
 */
final class JoinSpecification
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $condition;

    /**
     * @var null|string
     */
    private $alias;

    /**
     * JoinSpecification constructor.
     * @param string $table
     * @param string $condition
     * @param string $alias
     */
    public function __construct($table, $condition, $alias = null)
    {
        $this->table = $table;
        $this->condition = $condition;
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return null|string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    public function __toString()
    {
        $spec = $this->table;
        $spec .='.'. $this->condition;
        return $spec;
    }

}