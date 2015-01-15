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
 * Class SapphireBulkQueryRegister
 */
final class SapphireBulkQueryRegister implements IBulkQueryRegister {
    /**
     * @var IBulkQueryRegister
     */
    private static $instance;

    private function __construct(){
        $this->queries =  array();
    }

    private function __clone(){}

    /**
     * @return IBulkQueryRegister
     */
    public static function getInstance(){
        if(!is_object(self::$instance)){
            self::$instance = new SapphireBulkQueryRegister();
        }
        return self::$instance;
    }

    public function addBulkQuery(IBulkQuery $query){
        array_push( $this->queries , $query);
    }

    public function getQueries(){
        return $this->queries;
    }

    /**
     * @var IBulkQuery[]
     */
    private $queries;
} 