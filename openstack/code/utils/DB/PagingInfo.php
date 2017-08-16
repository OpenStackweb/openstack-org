<?php
/**
 * Copyright 2017 OpenStack Foundation
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

final class PagingInfo
{
    /**
     * @var int
     */
    private $page;
    /**
     * @var int
     */
    private $per_page;

    /**
     * @param int $page
     * @param int $per_page
     */
    public function __construct($page = 1, $per_page = 10)
    {
        $this->page     = $page;
        $this->per_page = $per_page;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->per_page;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return ($this->page - 1) * $this->per_page;
    }

    /**
     * @param int $count
     * @return int
     */
    public function getLastPage($count){
        return intval(ceil($count/$this->per_page));
    }
}