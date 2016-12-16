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
interface ISummitPresentationRepository extends ISummitEventRepository
{

    /**
     * @param int $summit_id
     * @param string $event_type
     * @param string $status
     * @param string $search_term
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getUnpublishedBySummit($summit_id, $event_type = null, $status = null, $search_term = null, $page = 1, $page_size = 10, $order = null);

    /**
     * @param int $summit_id
     * @param string $event_type
     * @param string $status
     * @param string $search_term
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getPublishedBySummit($summit_id, $event_type = null, $status = null, $search_term = null, $page = 1, $page_size = 10, $order = null);

    /**
     * @param int $summit_id
     * @param string $event_type
     * @param string $status
     * @param int $published
     * @param string $search_term
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getEventsBySummitPaged($summit_id, $event_type = null, $status = null, $published, $search_term = null, $page = 1, $page_size = 10, $order = null);

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getUnpublishedBySummitAndTrackList($summit_id, $track_list = null, $search_term = null, $page = 1 ,$page_size = 10, $order = null);

    /**
     * @param $summit_id
     * @param null $track
     * @param null $status
     * @param null $search_term
     * @param int $page
     * @param int $page_size
     * @param null $order
     * @return array
     */
    public function getUnpublishedBySummitAndTrack($summit_id, $track = null, $status = null, $search_term = null,  $page = 1 ,$page_size = 10,  $order = null);

    /**
     * @param int $track_id
     * @param int $page
     * @param int $page_size
     * @return IPresentation[]
     */
    public function getByCategoryPaged($track_id, $page, $page_size);

    /**
     * @param int $summit_id
     * @param string $date
     * @param string $tracks
     * @param string $venues
     * @param string $start_date
     * @param string $end_date
     * @param string $search_term
     * @return array
     */
    public function getPresentationMaterialBySummitAndDay($summit_id, $date, $tracks = 'all', $venues = 'all', $start_date, $end_date, $search_term);

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $sort
     * @param string $sort_dir
     * @param string $search_term
     * @param string $filter
     * @return IPresentation[]
     */
    public function getPresentationsAndSpeakersBySummit($summit_id, $page, $page_size, $sort, $sort_dir, $search_term, $filter);

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $sort
     * @param string $sort_dir
     * @param string $search_term
     * @return IPresentation[]
     */
    public function searchByCompanyPaged($summit_id,$page,$page_size,$sort,$sort_dir,$search_term);

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $sort
     * @param string $sort_dir
     * @param string $search_term
     * @param array $filters
     * @return IPresentation[]
     */
    public function searchByTrackPaged($summit_id,$page,$page_size,$sort,$sort_dir,$search_term,$filters);
}