<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Interface IAUCMetricRepository
 */
interface IAUCMetricRepository extends IEntityRepository
{
    /**
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param null|string $search_term
     * @param null|string $type
     * @param array $filter
     * @return array
     */
    function getAllByPage
    (
        $page         = 1,
        $page_size    = 10,
        $order        = '',
        $search_term  = null,
        $type         = null,
        array $filter = []
    );
}