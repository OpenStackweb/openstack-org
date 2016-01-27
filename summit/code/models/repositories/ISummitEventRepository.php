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
interface ISummitEventRepository extends IEntityRepository
{
    /**
     * @param ISummit $summit
     * @param string $term
     * @return ISummitEvent[]
     */
    public function searchBySummitAndTerm(ISummit $summit, $term);

    /**
     * @param $summit_id
     * @param int $event_type
     * @param int $page
     * @param int $page_size
     * @param null $order
     * @return array
     */
    public function getUnpublishedBySummit($summit_id, $event_type, $search_term, $page = 1, $page_size = 10, $order = null);

    /**
     * @param int $summit_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function getPublishedByTimeFrame($summit_id,$start_date, $end_date);
}