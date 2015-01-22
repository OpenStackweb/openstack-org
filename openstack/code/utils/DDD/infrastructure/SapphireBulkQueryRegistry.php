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
 * Class SapphireBulkQueryRegistry
 */
final class SapphireBulkQueryRegistry implements IBulkQueryRegistry {
    /**
     * @var IBulkQueryRegistry
     */
    private static $instance;

    /**
     * @var IBulkQuery[]
     */
    private $pre_queries;

    /**
     * @var IBulkQuery[]
     */
    private $post_queries;


    private function __construct(){
        $this->pre_queries =  array();
        $this->post_queries =  array();
    }

    private function __clone(){}

    /**
     * @return IBulkQueryRegistry
     */
    public static function getInstance(){
        if(!is_object(self::$instance)){
            self::$instance = new SapphireBulkQueryRegistry();
        }
        return self::$instance;
    }

    /**
     * @param IBulkQuery $query
     * @param string     $stage
     */
    public function addBulkQuery(IBulkQuery $query, $stage = 'pre'){
        if($stage == 'pre')
            array_push( $this->pre_queries , $query);
        else
            array_push( $this->post_queries , $query);
    }

    /**
     * @return IBulkQuery[]
     */
    public function getPreQueries()
    {
       return $this->pre_queries;
    }

    /**
     * @return IBulkQuery[]
     */
    public function getPostQueries()
    {
        return $this->post_queries;
    }
}