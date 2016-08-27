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
 * Interface IJobRepository
 */
interface IJobRepository extends IEntityRepository
{
    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getAllPosted($offset = 0, $limit = 10);

    /**
     * @param int $foundation
     * @return ArrayList
     */
    public function getDateSortedJobs($foundation = 0);

    /**
     * @param $keywords
     * @param $type_id
     * @param $sort_by
     * @return ArrayList
     */
    public function getJobsByKeywordTypeAndSortedBy($keywords, $type_id, $sort_by);

}