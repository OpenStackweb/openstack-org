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

/**
 * Class QueryCompoundCriteria
 */
class QueryCompoundCriteria extends QueryCriteria {


    /**
     * @var QueryCriteria[]
     */
    private $criterias;

    /**
     * @param string $operator
     * @param QueryCriteria[] $criterias
     */
    protected function __construct($operator, array $criterias)
    {
        parent::__construct(null, $operator, null);
        $this->criterias = $criterias;
    }

    /**
     * @param QueryCriteria[] $criterias
     * @return QueryCompoundCriteria
     */
    public static function compoundOr(array $criterias){
        return new QueryCompoundCriteria('OR', $criterias);
    }

    /**
     * @param QueryCriteria[] $criterias
     * @return QueryCompoundCriteria
     */
    public static function compoundAnd(array $criterias){
        return new QueryCompoundCriteria('AND', $criterias);
    }

    public function __toString(){
        $query = ' (';
        foreach ($this->criterias as $cnd) {
            $query .= (string)$cnd . ' '.$this->operator;
        }
        $query = trim($query, $this->operator);
        $query .= ') ';
        return $query;
    }
}