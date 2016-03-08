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

        $filter['Status'] = Presentation::STATUS_RECEIVED;

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
        if(is_null($order)) $order = " SummitSelectedPresentation.Order ASC ";

        $filter = '';
        if(!empty($track_list)){
            $filter = " AND SummitSelectedPresentationList.ID = {$track_list} ";
        }

        $selection_status_filter = '';
        if(!empty($status))
        {
            $selection_status_filter .= " WHERE SelectionStatus = '{$status}' ";
        }

        if (!empty($search_term)) {
            $search_term   = trim($search_term);
            $filter .= " AND (
            SummitEvent.Title LIKE '%{$search_term}%'
            OR SummitEvent.Description LIKE '%{$search_term}%'
            OR SummitEvent.ShortDescription LIKE '%{$search_term}%'
            OR EXISTS
                (
                        SELECT 1 FROM PresentationSpeaker
                        INNER JOIN Presentation_Speakers ON Presentation_Speakers.PresentationSpeakerID = PresentationSpeaker.ID
                        WHERE
                        Presentation_Speakers.PresentationID = Presentation.ID AND
                        (
                            PresentationSpeaker.FirstName LIKE '%{$search_term}%'
                            OR PresentationSpeaker.LastName LIKE '%{$search_term}%'
                            OR CONCAT(PresentationSpeaker.FirstName,' ',PresentationSpeaker.LastName) LIKE '%{$search_term}%'
                        )
                )
            )
            ";

        }

        $sql_count = <<<SQL
SELECT COUNT(ID) AS QTY FROM (
SELECT
DISTINCT Presentation.ID,
CASE WHEN SummitSelectedPresentation.`Order` IS NULL THEN 'unaccepted'
WHEN SummitSelectedPresentation.`Order` <= PresentationCategory.SessionCount THEN 'accepted'
ELSE 'alternate' END AS SelectionStatus
FROM SummitEvent
INNER JOIN Presentation  ON Presentation.ID = SummitEvent.ID
INNER JOIN PresentationCategory ON PresentationCategory.ID = Presentation.CategoryID
LEFT JOIN SummitSelectedPresentation ON SummitSelectedPresentation.PresentationID = Presentation.ID
LEFT JOIN SummitSelectedPresentationList ON SummitSelectedPresentation.SummitSelectedPresentationListID = SummitSelectedPresentationList.ID AND (ListType = 'Group') AND SummitSelectedPresentationList.CategoryID =  Presentation.CategoryID
WHERE
SummitEvent.Title IS NOT NULL
AND SummitEvent.Title <>''
AND SummitEvent.SummitID = {$summit_id}
AND SummitEvent.Published = 0
AND SummitSelectedPresentationList.ListType = 'Group'
AND SummitSelectedPresentationList.CategoryID = Presentation.CategoryID
{$filter}
) AS P {$selection_status_filter}
SQL;
        $offset    = ($page - 1 ) * $page_size;
        $sql = <<<SQL
SELECT * FROM (
SELECT DISTINCT
SummitEvent.Title,
SummitEvent.Description,
SummitEvent.ShortDescription,
SummitEvent.StartDate,
SummitEvent.EndDate,
SummitEvent.Published,
SummitEvent.PublishedDate,
SummitEvent.AllowFeedBack,
SummitEvent.AvgFeedbackRate,
SummitEvent.RSVPLink,
SummitEvent.HeadCount,
Presentation.*,
CASE WHEN SummitSelectedPresentation.`Order` IS NULL THEN 'unaccepted'
WHEN SummitSelectedPresentation.`Order` <= PresentationCategory.SessionCount THEN 'accepted'
ELSE 'alternate' END AS SelectionStatus
FROM SummitEvent
INNER JOIN Presentation  ON Presentation.ID = SummitEvent.ID
INNER JOIN PresentationCategory ON PresentationCategory.ID = Presentation.CategoryID
LEFT JOIN SummitSelectedPresentation ON SummitSelectedPresentation.PresentationID = Presentation.ID
LEFT JOIN SummitSelectedPresentationList ON SummitSelectedPresentation.SummitSelectedPresentationListID = SummitSelectedPresentationList.ID
WHERE
SummitEvent.Title IS NOT NULL
AND SummitEvent.Title <>''
AND SummitEvent.SummitID = {$summit_id}
AND SummitEvent.Published = 0
AND SummitSelectedPresentationList.ListType = 'Group'
AND SummitSelectedPresentationList.CategoryID = Presentation.CategoryID
{$filter}
ORDER BY {$order}
) AS P  {$selection_status_filter} LIMIT {$offset}, {$page_size}
SQL;


        $count = intval(DB::query($sql_count)->first()['QTY']);
        $data  = array();
        $res   = DB::query($sql);
        foreach($res as $row)
        {
            array_push($data, new Presentation($row));
        }
        return array($page, $page_size, $count,  $data);
    }

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
    public function getUnpublishedBySummitAndTrack($summit_id, $track = null, $status = null, $search_term = null,  $page = 1 ,$page_size = 10,  $order = null)
    {
        $filter = array('SummitID' => $summit_id, 'Published' => 0);

        $track_filter = '';
        if(!empty($track)){
            $track_filter = " AND CategoryID = {$track} ";
        }

        $filter['Status'] = Presentation::STATUS_RECEIVED;

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