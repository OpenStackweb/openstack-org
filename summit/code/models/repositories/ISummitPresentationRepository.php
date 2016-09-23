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
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getUnpublishedBySummit($summit_id, $event_type = null, $status = null, $search_term = null, $page = 1, $page_size = 10, $order = null);


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
}