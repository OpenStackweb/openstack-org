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
 * Class QueryCriteria
 */
class QueryCriteria
{

    protected $field;
    protected $operator;
    protected $value;
    protected $quoted;
    protected $is_id;
    protected $base_entity;


    /**
     * @param      $field
     * @param      $operator
     * @param      $value
     * @param bool $quoted
     * @param bool $is_id
     */
    protected function __construct($field, $operator, $value, $quoted = true, $is_id = false)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
        $this->quoted = $quoted;
        $this->is_id = $is_id;
    }

    public function setBaseEntity(IEntity $base_entity)
    {
        $this->base_entity = $base_entity;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function equal($field, $value, $quoted = true)
    {
        return new QueryCriteria($field, '=', $value, $quoted);
    }

    public static function id($field, $value)
    {
        return new QueryCriteria($field, '=', $value, true, true);
    }

    public static function notId($field, $value)
    {
        return new QueryCriteria($field, '<>', $value, true, true);
    }

    public static function notEqual($field, $value, $quoted = true)
    {
        return new QueryCriteria($field, '<>', $value, $quoted);
    }

    public static function greater($field, $value, $quoted = true, $is_id = false)
    {
        return new QueryCriteria($field, '>', $value, $quoted, $is_id);
    }

    public static function greaterOrEqual($field, $value, $quoted = true)
    {
        return new QueryCriteria($field, '>=', $value, $quoted);
    }

    public static function lower($field, $value, $quoted = true)
    {
        return new QueryCriteria($field, '<', $value, $quoted);
    }

    public static function lowerOrEqual($field, $value, $quoted = true)
    {
        return new QueryCriteria($field, '<=', $value, $quoted);
    }

    public static function like($field, $value, $quoted = true)
    {
        return new QueryCriteria($field, 'LIKE', '%' . $value . '%', $quoted);
    }

    public static function isNull($field, $quoted = true)
    {
        return new QueryCriteria($field, 'IS', "NULL", $quoted);
    }

    public function __toString()
    {
        $field = $this->field;

        if (!$this->is_id && strpos($field, 'ID') > 0)
            $field = str_replace('.', '', $field);
        if ($this->is_id && strtoupper($field) === 'ID') {
            $class_name = ClassInfo::baseDataClass($this->base_entity);
            $field = sprintf('%s.%s', $class_name, 'ID');
        }
        if (strpos($field, '.')) {
            $parts = explode('.', $field);
            $parsed_field = '';
            foreach ($parts as $part) {
                if ($this->quoted)
                    $parsed_field .= sprintf('`%s`.', $part);
                else
                    $parsed_field .= sprintf('%s.', $part);
            }
            $field = trim($parsed_field, '.');
        } else {
            $field = sprintf('%s', $field);
            if ($this->quoted)
                $field = '`' . $field . '`';
        }

        return is_string($this->value) && $this->value !== 'NULL' ?
            sprintf(" %s %s '%s' ", $field, $this->operator, Convert::raw2sql($this->value)) :
            sprintf(" %s %s %s ", $field, $this->operator, Convert::raw2sql($this->value));
    }
} 