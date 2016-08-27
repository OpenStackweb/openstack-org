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
interface ISummitAssistanceRepository extends IEntityRepository
{
    /**
     * @param int $summit_id
     * @return ArrayList
     */
    public function getAssistanceBySummit($summit_id, $page, $page_size, $sort, $sort_dir, $filter);

    //TODO : this does not belongs here, move to another repository !!!
    public function getRoomsBySummitAndDay($summit_id, $date, $event_type, $venue);

    //TODO : this does not belongs here, move to another repository !!!
    public function getPresentationsAndSpeakersBySummit($summit_id, $page, $page_size, $sort, $sort_dir, $search_term, $filter);

    //TODO : this does not belongs here, move to another repository !!!
    public function getPresentationMaterialBySummitAndDay($summit_id, $date, $tracks = 'all', $venues = 'all', $start_date, $end_date, $search_term);

}