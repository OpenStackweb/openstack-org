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
 * Class SapphireSummitPresentationRepository
 */
final class SapphireSummitPresentationRepository extends SapphireSummitEventRepository implements ISummitPresentationRepository
{

    public function __construct()
    {
        parent::__construct(new Presentation());
    }

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getUnpublishedBySummit($summit_id, $event_type = null, $status = null, $search_term = null, $page = 1, $page_size = 10, $order = null)
    {
        $filter = array('SummitID' => $summit_id, 'Published' => 0);
        if(is_null($order)) $order = array('SummitEvent.Created' => 'ASC');

        $where_clause = "SummitEvent.Title IS NOT NULL AND SummitEvent.Title <>'' AND SummitEventType.Type = 'Presentation'";
        if ($search_term) {
            $where_clause .= " AND (SummitEvent.Title LIKE '%{$search_term}%' OR SummitEvent.Description LIKE '%{$search_term}%'";
            $where_clause .= " OR PresentationSpeaker.FirstName LIKE '%{$search_term}%' OR PresentationSpeaker.LastName LIKE '%{$search_term}%'";
            $where_clause .= " OR CONCAT(PresentationSpeaker.FirstName,' ',PresentationSpeaker.LastName) LIKE '%{$search_term}%' )";
        }

        if(!empty($status)){
            $filter['Status'] = $status;
        }

        $list      = Presentation::get()
                        ->leftJoin('Presentation_Speakers','Presentation_Speakers.PresentationID = Presentation.ID')
                        ->leftJoin('PresentationSpeaker','Presentation_Speakers.PresentationSpeakerID = PresentationSpeaker.ID')
                        ->leftJoin("SummitEventType","SummitEventType.ID = SummitEvent.TypeID")
                        ->filter($filter)
                        ->where($where_clause)
                        ->sort("TRIM({$order})");

        $count     = intval($list->count());
        if ($page_size) {
            $offset    = ($page - 1 ) * $page_size;
            $data      = $list->limit($page_size, $offset);
        } else {
            $data = $list;
        }

        return array($page, $page_size, $count, $data);
    }

    /**
     * @param int $summit_id
     * @param null $track_list
     * @param null $search_term
     * @param int $page
     * @param int $page_size
     * @param null $order
     * @return array
     */
    public function getUnpublishedBySummitAndTrackList($summit_id, $track_list = null, $status = null, $search_term = null,  $page = 1 ,$page_size = 10,  $order = null)
    {
        if(is_null($order)) $order = array('SummitSelectedPresentation.Order' => 'ASC');
        $filter = array('SummitID' => $summit_id, 'Published' => 0);

        $track_filter = '';
        if(!empty($track_list)){
            $track_filter = " AND SummitSelectedPresentationList.ID = {$track_list} ";
        }

        if(!empty($status)){
            $filter['Status'] = $status;
        }

        $where_clause = " SummitEvent.Title IS NOT NULL AND SummitEvent.Title <>'' ";
        if ($search_term) {
            $where_clause .= "AND (SummitEvent.Title LIKE '%{$search_term}%' OR SummitEvent.Description LIKE '%{$search_term}%'";
            $where_clause .= " OR PresentationSpeaker.FirstName LIKE '%{$search_term}%' OR PresentationSpeaker.LastName LIKE '%{$search_term}%'";
            $where_clause .= " OR CONCAT(PresentationSpeaker.FirstName,' ',PresentationSpeaker.LastName) LIKE '%{$search_term}%' ) ";
        }

        $list = Presentation::get()->filter($filter)->where($where_clause)
            ->leftJoin('Presentation_Speakers','Presentation_Speakers.PresentationID = Presentation.ID')
            ->leftJoin('PresentationSpeaker','Presentation_Speakers.PresentationSpeakerID = PresentationSpeaker.ID')
            ->innerJoin('SummitSelectedPresentation', 'SummitSelectedPresentation.PresentationID = Presentation.ID')
                ->innerJoin('SummitSelectedPresentationList', "SummitSelectedPresentation.SummitSelectedPresentationListID = SummitSelectedPresentationList.ID AND (ListType = 'Group') {$track_filter}")
                ->sort("TRIM({$order})");

        $count     = intval($list->count());
        if ($page_size) {
            $offset    = ($page - 1 ) * $page_size;
            $data      = $list->limit($page_size, $offset);
        } else {
            $data = $list;
        }

        return array($page, $page_size, $count,  $data);
    }

    /**
     * @param int $summit_id
     * @param null $track_list
     * @param null $search_term
     * @param int $page
     * @param int $page_size
     * @param null $order
     * @return array
     */
    public function getUnpublishedBySummitAndTrack($summit_id, $track = null, $status = null, $search_term = null,  $page = 1 ,$page_size = 10,  $order = null)
    {
        $filter = array('SummitID' => $summit_id, 'Published' => 0);

        $track_filter = '';
        if(!empty($track)){
            $track_filter = " AND CategoryID = {$track} ";
        }

        if(!empty($status)){
            $filter['Status'] = $status;
        }

        $where_clause = " SummitEvent.Title IS NOT NULL AND SummitEvent.Title <>'' ";
        if ($search_term) {
            $where_clause .= "AND (SummitEvent.Title LIKE '%{$search_term}%' OR SummitEvent.Description LIKE '%{$search_term}%'";
            $where_clause .= " OR PresentationSpeaker.FirstName LIKE '%{$search_term}%' OR PresentationSpeaker.LastName LIKE '%{$search_term}%'";
            $where_clause .= " OR CONCAT(PresentationSpeaker.FirstName,' ',PresentationSpeaker.LastName) LIKE '%{$search_term}%' ) ";
        }

        $where_clause .= $track_filter;

        $list = Presentation::get()->filter($filter)->where($where_clause)
            ->leftJoin('Presentation_Speakers','Presentation_Speakers.PresentationID = Presentation.ID')
            ->leftJoin('PresentationSpeaker','Presentation_Speakers.PresentationSpeakerID = PresentationSpeaker.ID')
            ->sort("TRIM({$order})");

        $count     = intval($list->count());
        if ($page_size) {
            $offset    = ($page - 1 ) * $page_size;
            $data      = $list->limit($page_size, $offset);
        } else {
            $data = $list;
        }

        return array($page, $page_size, $count,  $data);
    }
}