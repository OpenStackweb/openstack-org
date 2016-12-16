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
     * @param string $event_type
     * @param string $status
     * @param string $search_term
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getUnpublishedBySummit($summit_id, $event_type = null, $status = null, $search_term = null, $page = 1, $page_size = 10, $order = null)
    {
        return $this->getEventsBySummitPaged($summit_id, $event_type, $status, 0, $search_term, $page, $page_size, $order);
    }

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
    public function getPublishedBySummit($summit_id, $event_type = null, $status = null, $search_term = null, $page = 1, $page_size = 10, $order = null)
    {
        return $this->getEventsBySummitPaged($summit_id, $event_type, $status, 1, $search_term, $page, $page_size, $order);
    }

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
    public function getEventsBySummitPaged($summit_id, $event_type = null, $status = null, $published, $search_term = null, $page = 1, $page_size = 10, $order = null)
    {
        $filter = array('SummitID' => $summit_id, 'Published' => $published);
        if(is_null($order)) $order = 'SummitEvent.Created';

        $where_clause = "SummitEvent.Title IS NOT NULL AND SummitEvent.Title <>'' AND (SummitEventType.Type IN ('Presentation','Panel','Keynotes')) ";
        if ($search_term) {
            $where_clause .= " AND (SummitEvent.Title LIKE '%{$search_term}%' OR SummitEvent.Abstract LIKE '%{$search_term}%'";
            $where_clause .= " OR PS.FirstName LIKE '%{$search_term}%' OR PS.LastName LIKE '%{$search_term}%'";
            $where_clause .= " OR CONCAT(PS.FirstName,' ',PS.LastName) LIKE '%{$search_term}%'";
            $where_clause .= " OR PS2.FirstName LIKE '%{$search_term}%' OR PS2.LastName LIKE '%{$search_term}%'";
            $where_clause .= " OR CONCAT(PS2.FirstName,' ',PS2.LastName) LIKE '%{$search_term}%'";
            $where_clause .= " OR PS.ID = '{$search_term}' OR PS2.ID = '{$search_term}' OR SummitEvent.ID = '{$search_term}'";
            $where_clause .= " OR M.Email LIKE '%{$search_term}%' OR M2.Email LIKE '%{$search_term}%' OR SRR.Email LIKE '%{$search_term}%' )";
        }

        $filter['Status'] = Presentation::STATUS_RECEIVED;

        $list      = Presentation::get()
            ->leftJoin('Presentation_Speakers','Presentation_Speakers.PresentationID = Presentation.ID')
            ->leftJoin('PresentationSpeaker','Presentation_Speakers.PresentationSpeakerID = PS.ID','PS')
            ->leftJoin('PresentationSpeaker','Presentation.ModeratorID = PS2.ID','PS2')
            ->leftJoin('Member','M.ID = PS.MemberID','M')
            ->leftJoin('Member','M2.ID = PS2.MemberID','M2')
            ->leftJoin('SpeakerRegistrationRequest','SRR.SpeakerID = PS.ID','SRR')
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
            OR SummitEvent.Abstract LIKE '%{$search_term}%'
            OR SummitEvent.ID = '{$search_term}'
            OR EXISTS
                (
                        SELECT 1 FROM PresentationSpeaker
                        INNER JOIN Presentation_Speakers ON Presentation_Speakers.PresentationSpeakerID = PresentationSpeaker.ID
                        LEFT JOIN Member AS M ON M.ID = PresentationSpeaker.MemberID
                        WHERE
                        Presentation_Speakers.PresentationID = Presentation.ID AND
                        (
                            PresentationSpeaker.FirstName LIKE '%{$search_term}%'
                            OR PresentationSpeaker.LastName LIKE '%{$search_term}%'
                            OR CONCAT(PresentationSpeaker.FirstName,' ',PresentationSpeaker.LastName) LIKE '%{$search_term}%'
                            OR PresentationSpeaker.ID = '{$search_term}'
                            OR M.Email LIKE '%{$search_term}%'
                        )
                )
            OR EXISTS
                (
                        SELECT 1 FROM PresentationSpeaker
                        LEFT JOIN Member AS M ON M.ID = PresentationSpeaker.MemberID
                        WHERE
                        PresentationSpeaker.ID = Presentation.ModeratorID AND
                        (
                            PresentationSpeaker.FirstName LIKE '%{$search_term}%'
                            OR PresentationSpeaker.LastName LIKE '%{$search_term}%'
                            OR CONCAT(PresentationSpeaker.FirstName,' ',PresentationSpeaker.LastName) LIKE '%{$search_term}%'
                            OR PresentationSpeaker.ID = '{$search_term}'
                            OR M.Email LIKE '%{$search_term}%'
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
INNER JOIN PresentationCategory ON PresentationCategory.ID = SummitEvent.CategoryID
LEFT JOIN SummitSelectedPresentation ON SummitSelectedPresentation.PresentationID = Presentation.ID
LEFT JOIN SummitSelectedPresentationList ON SummitSelectedPresentation.SummitSelectedPresentationListID = SummitSelectedPresentationList.ID AND (ListType = 'Group') AND SummitSelectedPresentationList.CategoryID =  SummitEvent.CategoryID
WHERE
SummitEvent.Title IS NOT NULL
AND SummitEvent.Title <>''
AND SummitEvent.SummitID = {$summit_id}
AND SummitEvent.Published = 0
AND SummitSelectedPresentationList.ListType = 'Group'
AND SummitSelectedPresentationList.CategoryID = SummitEvent.CategoryID
{$filter}
) AS P {$selection_status_filter}
SQL;
        $offset    = ($page - 1 ) * $page_size;
        $sql = <<<SQL
SELECT * FROM (
SELECT DISTINCT
SummitEvent.Title,
SummitEvent.Abstract,
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
INNER JOIN PresentationCategory ON PresentationCategory.ID = SummitEvent.CategoryID
LEFT JOIN SummitSelectedPresentation ON SummitSelectedPresentation.PresentationID = Presentation.ID
LEFT JOIN SummitSelectedPresentationList ON SummitSelectedPresentation.SummitSelectedPresentationListID = SummitSelectedPresentationList.ID
WHERE
SummitEvent.Title IS NOT NULL
AND SummitEvent.Title <>''
AND SummitEvent.SummitID = {$summit_id}
AND SummitEvent.Published = 0
AND SummitSelectedPresentationList.ListType = 'Group'
AND SummitSelectedPresentationList.CategoryID = SummitEvent.CategoryID
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
            $where_clause .= " AND (SummitEvent.Title LIKE '%{$search_term}%' OR SummitEvent.Abstract LIKE '%{$search_term}%'";
            $where_clause .= " OR PS.FirstName LIKE '%{$search_term}%' OR PS.LastName LIKE '%{$search_term}%'";
            $where_clause .= " OR CONCAT(PS.FirstName,' ',PS.LastName) LIKE '%{$search_term}%'";
            $where_clause .= " OR PS2.FirstName LIKE '%{$search_term}%' OR PS2.LastName LIKE '%{$search_term}%'";
            $where_clause .= " OR CONCAT(PS2.FirstName,' ',PS2.LastName) LIKE '%{$search_term}%'";
            $where_clause .= " OR PS.ID = '{$search_term}' OR PS2.ID = '{$search_term}' OR SummitEvent.ID = '{$search_term}'";
            $where_clause .= " OR M.Email LIKE '%{$search_term}%' OR M2.Email LIKE '%{$search_term}%' OR SRR.Email LIKE '%{$search_term}%' )";
        }

        $where_clause .= $track_filter;

        $list = Presentation::get()->filter($filter)->where($where_clause)
            ->leftJoin('Presentation_Speakers','Presentation_Speakers.PresentationID = Presentation.ID')
            ->leftJoin('PresentationSpeaker','Presentation_Speakers.PresentationSpeakerID = PS.ID','PS')
            ->leftJoin('PresentationSpeaker','Presentation.ModeratorID = PS2.ID','PS2')
            ->leftJoin('Member','M.ID = PS.MemberID','M')
            ->leftJoin('Member','M2.ID = PS2.MemberID','M2')
            ->leftJoin('SpeakerRegistrationRequest','SRR.SpeakerID = PS.ID','SRR')
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
     * @param int $track_id
     * @param int $page
     * @param int $page_size
     * @return IPresentation[]
     */
    public function getByCategoryPaged($track_id, $page, $page_size)
    {
        $offset = ($page-1) * $page_size;
        return Presentation::get()->filter('CategoryID', $track_id)->limit($page_size, $offset);
    }

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
    public function getPresentationsAndSpeakersBySummit($summit_id, $page, $page_size, $sort, $sort_dir, $search_term, $filter)
    {

        $query_body = <<<SQL

FROM SummitEvent AS E
INNER JOIN Presentation ON Presentation.ID = E.ID
INNER JOIN Presentation_Speakers ON Presentation_Speakers.PresentationID = Presentation.ID
INNER JOIN PresentationSpeaker AS S ON S.ID = Presentation_Speakers.PresentationSpeakerID
INNER JOIN PresentationCategory  ON PresentationCategory.ID = E.CategoryID
LEFT JOIN Member ON Member.ID = S.MemberID
LEFT JOIN SpeakerRegistrationRequest ON SpeakerRegistrationRequest.SpeakerID = S.ID
LEFT JOIN PresentationSpeakerSummitAssistanceConfirmationRequest AS ACR ON ACR.SpeakerID = S.ID AND ACR.SummitID = {$summit_id}
LEFT JOIN SummitAbstractLocation AS L ON L.ID = E.LocationID
LEFT JOIN (
    SELECT Type, Code, SpeakerID FROM SpeakerSummitRegistrationPromoCode
    INNER JOIN SummitRegistrationPromoCode ON SummitRegistrationPromoCode.ID = SpeakerSummitRegistrationPromoCode.ID
    AND SummitRegistrationPromoCode.SummitID = $summit_id
) AS PromoCodes ON PromoCodes.SpeakerID = S.ID
WHERE
E.SummitID = {$summit_id}
AND E.Published = 1

SQL;


        if ($search_term) {
            $query_body .= " AND (E.Title LIKE '%{$search_term}%' OR S.FirstName LIKE '%{$search_term}%'
                            OR S.LastName LIKE '%{$search_term}%' OR CONCAT(S.FirstName,' ',S.LastName) LIKE '%{$search_term}%')";
        }

        if ($filter != 'all') {
            if ($filter == 'hide_confirmed')
                $query_body .= " AND ACR.IsConfirmed = 0";
            else if ($filter == 'hide_registered')
                $query_body .= " AND ACR.RegisteredForSummit = 0";
            else if ($filter == 'hide_checkedin')
                $query_body .= " AND ACR.CheckedIn = 0";
            else if ($filter == 'hide_all')
                $query_body .= " AND ACR.IsConfirmed = 0 AND ACR.RegisteredForSummit = 0 AND ACR.CheckedIn = 0";
        }

        $query_count = "SELECT COUNT(*) ";

        $query = <<<SQL
        SELECT
        E.ID AS P_ID,
        E.Title AS Presentation,
        E.Published AS Published,
        PresentationCategory.Title AS Track,
        E.StartDate AS Start_Date,
        L.Name AS Location,
        S.ID AS Speaker_id,
        Member.ID AS Member_id,
       IFNULL(CONCAT(Member.FirstName ,' ',Member.Surname), CONCAT(S.FirstName ,' ',S.LastName)) AS Name,
        IFNULL(Member.Email, SpeakerRegistrationRequest.Email) AS Email,
        PromoCodes.Code AS Code,
        PromoCodes.Type AS Type,
        ACR.IsConfirmed AS Confirmed,
        ACR.RegisteredForSummit AS Registered,
        ACR.CheckedIn AS Checked_in,
        ACR.OnSitePhoneNumber AS Phone,
        ACR.ID AS Assistance_id
SQL;

        $query .= $query_body." ORDER BY {$sort} {$sort_dir}";
        $query_count .= $query_body;

        if ($page && $page_size) {
            $offset = ($page - 1 ) * $page_size;
            $query .= " LIMIT {$offset}, {$page_size}";
        }

        $data = DB::query($query);
        $total = DB::query($query_count)->value();
        $result = array('Total' => $total, 'Data' => $data);
        return $result;
    }


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
    public function getPresentationMaterialBySummitAndDay($summit_id, $date, $tracks = 'all', $venues = 'all', $start_date, $end_date, $search_term)
    {

        $query = <<<SQL
SELECT E.ID AS id ,
0 AS date,
0 AS time,
E.StartDate AS start_date,
E.EndDate AS end_date,
GROUP_CONCAT(DISTINCT(CONCAT(S.FirstName,' ',S.LastName))) AS speakers,
GROUP_CONCAT(DISTINCT(T.Tag)) AS tags,
E.Title AS event,
E.Abstract AS description,
L.Name AS room,
L2.Name AS venue,
PM.DisplayOnSite as display,
PV.YouTubeID as youtube_id
FROM SummitEvent AS E
LEFT JOIN Presentation AS P ON P.ID = E.ID
LEFT JOIN Presentation_Speakers AS PS ON PS.PresentationID = P.ID
LEFT JOIN PresentationSpeaker AS S ON S.ID = PS.PresentationSpeakerID
LEFT JOIN SummitEvent_Tags AS ET ON ET.SummitEventID = E.ID
LEFT JOIN Tag AS T ON T.ID = ET.TagID
LEFT JOIN SummitAbstractLocation AS L ON L.ID = E.LocationID
LEFT JOIN SummitVenueRoom AS R ON R.ID = L.ID
LEFT JOIN SummitAbstractLocation AS L2 ON L2.ID = R.VenueID
LEFT JOIN PresentationMaterial AS PM ON P.ID = PM.PresentationID
LEFT JOIN PresentationVideo AS PV ON PM.ID = PV.ID
LEFT JOIN SummitEvent_Tags AS ETag ON E.ID = ETag.SummitEventID
LEFT JOIN Tag ON Tag.ID = ETag.TagID
WHERE DATE(E.StartDate) = '{$date}' AND E.SummitID = {$summit_id}
AND E.ClassName = 'Presentation' AND PM.ClassName = 'PresentationVideo'
SQL;

        if ($tracks && $tracks != 'all') {
            $query .= <<<SQL
 AND E.CategoryID IN ( {$tracks} )
SQL;
        }

        if ($venues && $venues != 'all') {
            $query .= <<<SQL
 AND E.LocationID IN ( {$venues} )
SQL;
        }

        if ($start_date) {
            $query .= <<<SQL
 AND DATE(E.StartDate) >= '$start_date'
SQL;
        }

        if ($end_date) {
            $query .= <<<SQL
 AND DATE(E.EndDate) <= '$end_date'
SQL;
        }

        if ($search_term) {
            $query .= <<<SQL
 AND (E.Title LIKE '%$search_term%' OR E.Abstract LIKE '%$search_term%' OR Tag.Tag LIKE '%$search_term%' )
SQL;
        }

        $query .= <<<SQL
 GROUP BY E.ID
SQL;

        return DB::query($query);
    }

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $sort
     * @param string $sort_dir
     * @param string $search_term
     * @return IPresentation[]
     */
    public function searchByCompanyPaged($summit_id,$page,$page_size,$sort,$sort_dir,$search_term)
    {
        $status_received = Presentation::STATUS_RECEIVED;
        $query_body = <<<SQL
            FROM SummitEvent AS E
            INNER JOIN Presentation AS P ON E.ID = P.ID
            INNER JOIN PresentationCategory AS PC ON E.CategoryID = PC.ID
            INNER JOIN Presentation_Speakers AS PS ON PS.PresentationID = P.ID
            INNER JOIN PresentationSpeaker AS S ON PS.PresentationSpeakerID = S.ID
            LEFT JOIN SpeakerRegistrationRequest AS SR ON SR.SpeakerID = S.ID
            INNER JOIN Member AS M ON M.ID = S.MemberID
            INNER JOIN Affiliation AS A ON A.MemberID=M.ID
            INNER JOIN Org ON Org.ID = A.OrganizationID
            WHERE E.SummitID = {$summit_id} AND E.Title IS NOT NULL
            AND P.Status='{$status_received}' AND E.LocationID <> 0 AND A.Current = 1
SQL;

        if ($search_term) {
            $query_body .= " AND (E.Title LIKE '%{$search_term}%' OR S.FirstName LIKE '%{$search_term}%'
                            OR S.LastName LIKE '%{$search_term}%' OR CONCAT(S.FirstName,' ',S.LastName) LIKE '%{$search_term}%'
                            OR PC.Title LIKE '%{$search_term}%' OR M.Email LIKE '%{$search_term}%' OR Org.Name LIKE '%{$search_term}%'
                            OR SR.Email LIKE '%{$search_term}%'
                            OR EXISTS (
                                SELECT * FROM SummitEvent_Tags
                                LEFT JOIN Tag ON Tag.ID = SummitEvent_Tags.TagID
                                WHERE SummitEventID = P.ID AND Tag.Tag LIKE '%{$search_term}%'
                                )
                            )";
        }

        $query_body .= " GROUP BY P.ID, S.ID, Org.ID ";

        $query_count = "SELECT COUNT(*) FROM (SELECT P.ID ";

        $query = <<<SQL
        SELECT
        P.ID AS presentation_id,
        CONCAT('https://www.openstack.org/summit/barcelona-2016/summit-schedule/events/',P.ID,'/') AS url ,
        E.Title AS title,
        E.Abstract AS description,
        PC.Title AS track,
        S.FirstName AS first_name,
        S.LastName AS last_name,
        M.Email AS email,
        Org.Name AS company
SQL;

        $query .= $query_body." ORDER BY {$sort} {$sort_dir}";
        $query_count .= $query_body." ) AS Q1";

        if ($page && $page_size) {
            $offset = ($page - 1 ) * $page_size;
            $query .= " LIMIT {$offset}, {$page_size}";
        }

        $data = array();
        foreach (DB::query($query) as $row) {
            $data[] = $row;
        }
        $total = DB::query($query_count)->value();
        $result = array('Total' => $total, 'Data' => $data);
        return $result;
    }

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
    public function searchByTrackPaged($summit_id,$page,$page_size,$sort,$sort_dir,$search_term,$filters)
    {
        $status_received = Presentation::STATUS_RECEIVED;

        $query_body = <<<SQL
            FROM SummitEvent AS E
            INNER JOIN Presentation AS P ON E.ID = P.ID
            INNER JOIN PresentationCategory AS PC ON E.CategoryID = PC.ID
            INNER JOIN Presentation_Speakers AS PS ON PS.PresentationID = P.ID
            INNER JOIN PresentationSpeaker AS S ON PS.PresentationSpeakerID = S.ID
            LEFT JOIN SpeakerRegistrationRequest AS SR ON SR.SpeakerID = S.ID
            INNER JOIN Member AS M ON M.ID = S.MemberID
            INNER JOIN Member AS M2 ON M2.ID = P.ModeratorID
            INNER JOIN Member AS M3 ON M3.ID = P.CreatorID
            INNER JOIN Affiliation AS A ON A.MemberID=M.ID
            INNER JOIN Org ON Org.ID = A.OrganizationID
            WHERE E.SummitID = {$summit_id} AND E.Title IS NOT NULL
            AND A.Current = 1
SQL;

        if ($filters['status']) {
            $query_body .= ($filters['status'] == 'null') ? " AND P.Status IS NULL" : " AND P.Status='{$filters['status']}'";
        }

        if ($filters['published']) {
            $query_body .= ($filters['published'] == 'scheduled') ? " AND E.LocationID <> 0" : " AND E.LocationID = 0";
        }

        if ($filters['track']) {
            $tracks = implode(',', $filters['track']);
            $query_body .= " AND E.CategoryID IN({$tracks})";
        }

        if ($search_term) {
            $query_body .= " AND (E.Title LIKE '%{$search_term}%' OR S.FirstName LIKE '%{$search_term}%'
                            OR S.LastName LIKE '%{$search_term}%' OR CONCAT(S.FirstName,' ',S.LastName) LIKE '%{$search_term}%'
                            OR PC.Title LIKE '%{$search_term}%' OR M.Email LIKE '%{$search_term}%' OR Org.Name LIKE '%{$search_term}%'
                            OR SR.Email LIKE '%{$search_term}%'
                            OR EXISTS (
                                SELECT * FROM SummitEvent_Tags
                                LEFT JOIN Tag ON Tag.ID = SummitEvent_Tags.TagID
                                WHERE SummitEventID = P.ID AND Tag.Tag LIKE '%{$search_term}%'
                                )
                            )";
        }

        $query_group = " GROUP BY P.ID";
        if (in_array('speaker',$filters['show_col'])) {
            $query_group .= ", S.ID ";
        }

        $query_count = "SELECT COUNT(*) FROM (SELECT P.ID ";

        $query = <<<SQL
        SELECT
        P.ID AS id,
        E.Title AS title,
        PC.Title AS track,
SQL;

        if (in_array('speaker',$filters['show_col'])) {
            $query .= "S.FirstName AS first_name,
                       S.LastName AS last_name,
                       M.Email AS email,
                       GROUP_CONCAT(Org.Name SEPARATOR ', ') AS company,";
        }

        if (in_array('moderator',$filters['show_col'])) {
            $query .= "M2.Email AS moderator_email,";
        }

        if (in_array('owner',$filters['show_col'])) {
            $query .= "M3.Email AS owner_email,";
        }

        $query .= "P.Status AS status";

        $query .= $query_body.$query_group." ORDER BY {$sort} {$sort_dir}";
        $query_count .= $query_body." ) AS Q1";
        $query_track = "SELECT PC.Title AS track, COUNT(PC.ID) AS track_count " . $query_body . " GROUP BY PC.ID ORDER BY PC.Title";

        if ($page && $page_size) {
            $offset = ($page - 1 ) * $page_size;
            $query .= " LIMIT {$offset}, {$page_size}";
        }

        $data = array();
        foreach (DB::query($query) as $row) {
            $data[] = $row;
        }

        $total = DB::query($query_count)->value();
        $track_count = DB::query($query_track)->map();
        $result = array('total' => $total, 'data' => $data, 'track_count' => $track_count);
        return $result;
    }
}