<?php
/**
 * Copyright 2017 Open Infrastructure Foundation
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

final class OrderElement
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $direction;

    /**
     * OrderElement constructor.
     * @param $field
     * @param $direction
     */
    private function __construct($field, $direction)
    {
        $this->field     = $field;
        $this->direction = $direction;
    }

    public static function buildAscFor($field)
    {
        return new OrderElement($field, 'ASC');
    }

    public static function buildDescFor($field)
    {
        return new OrderElement($field, 'DESC');
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    public function isAsc()
    {
        return $this->direction === 'ASC';
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }
}