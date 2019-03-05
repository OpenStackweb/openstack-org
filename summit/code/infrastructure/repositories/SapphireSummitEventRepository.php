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
 * Class SapphireSummitEventRepository
 */
class SapphireSummitEventRepository extends SapphireRepository implements ISummitEventRepository
{

    public function __construct()
    {
        parent::__construct(new SummitEvent());
    }

    public function getPresentationById($event_id)
    {
        return Presentation::get_by_id('Presentation',$event_id);
    }

    /**
     * @param ISummit $summit
     * @param string $term
     * @return ISummitEvent[]
     */
    public function searchBySummitAndTerm(ISummit $summit, $term)
    {
        if(empty($term)) return [];
        $events    = [];

        $summit_id = $summit->getIdentifier();

        $sql_events   = <<<SQL
        SELECT * FROM (
            SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1
                AND (Title LIKE '%{$term}%' OR E.ID = '{$term}')
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1
                AND EXISTS
                (
                    SELECT T.ID FROM Tag T INNER JOIN SummitEvent_Tags ET ON ET.TagID = T.ID
                    WHERE ET.SummitEventID = E.ID AND T.Tag LIKE '%{$term}%'
                )
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1
                AND EXISTS
                (
                    SELECT C.ID FROM Company C INNER JOIN SummitEvent_Sponsors ES ON ES.CompanyID = C.ID
                    WHERE ES.SummitEventID = E.ID AND C.Name LIKE '%{$term}%'
                )
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1
                AND EXISTS
                (
                    SELECT P.ID FROM Presentation P
                    INNER JOIN SummitEvent E2 ON E2.ID = P.ID                   
                    LEFT JOIN PresentationCategory PC ON PC.ID = E2.CategoryID
                    WHERE  P.ID = E.ID AND PC.Title LIKE '%{$term}%'
                )
            UNION SELECT DISTINCT E.* FROM SummitEvent E LEFT JOIN SummitEventType ET ON ET.ID = E.TypeID
                WHERE E.SummitID = {$summit_id} AND E.Published = 1
                AND ET.Type LIKE '%{$term}%'
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1
                AND EXISTS
                (
                    SELECT P.ID FROM Presentation P
                    WHERE  P.ID = E.ID AND P.Level LIKE '%{$term}%'
                )
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1
                AND EXISTS
                (
                    SELECT P.ID, CONCAT(S.FirstName,' ',S.LastName) AS SpeakerFullName  From Presentation P
                    INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
                    INNER JOIN PresentationSpeaker S ON S.ID = PS.PresentationSpeakerID
                    WHERE P.ID = E.ID
                    HAVING
                        SpeakerFullName LIKE '%{$term}%'
                        OR SOUNDEX(SpeakerFullName) = SOUNDEX('{$term}')
                )
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1
                AND EXISTS
                (
                    SELECT P.ID From Presentation P
                    INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
                    INNER JOIN PresentationSpeaker S ON S.ID = PS.PresentationSpeakerID
                    LEFT JOIN Member M ON M.ID = S.MemberID
                    LEFT JOIN Affiliation A ON A.MemberID = S.MemberID
                    LEFT JOIN Org ON Org.ID = A.OrganizationID
                    WHERE P.ID = E.ID AND (S.ID = '{$term}' OR M.Email LIKE '{$term}' OR Org.Name LIKE '%{$term}%')
                )
SQL;

        foreach(DB::query($sql_events.") AS Q1 ORDER BY StartDate ASC, EndDate ASC ;") as $row)
        {
            $class = $row['ClassName'];
            $event = new $class($row);
            if(!ScheduleManager::allowToSee($event)) continue;
            $events[] = $event;
        }

        return $events;
    }

    /**
     * @param int $summit_id
     * @param array $event_types
     * @param array $filters
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getUnpublishedBySummit($summit_id, $event_types = [], $filters, $page = 1, $page_size = 10, $order = null)
    {
        $selection_status = $filters['status'];
        $search_term = $filters['search_term'];
        $track_id = $filters['track_id'];

        if(is_null($order)) $order = array('SummitEvent.Created' => 'ASC');

        $where_clause = "SummitEvent.Title IS NOT NULL AND SummitEvent.Title <> '' ";
        $where_clause .= !empty($track_id) ? " AND SummitEvent.CategoryID = {$track_id} " : "";

        if (count($event_types)) {
            $event_types_csv = "'".implode("','",$event_types)."'";
            $where_clause .= " AND SummitEventType.Type IN (".$event_types_csv.") ";
        }

        if ($selection_status) {
            switch ($selection_status) {
                case 'selected' :
                    $where_clause .= " AND SSP.`Order` IS NOT NULL AND SSPL.ListType = 'Group'";
                    $where_clause .= " AND SSPL.CategoryID = SummitEvent.CategoryID";
                    break;
                case 'accepted' :
                    $where_clause .= " AND SSP.`Order` IS NOT NULL AND SSP.`Order` <= PC.SessionCount AND SSPL.ListType = 'Group'";
                    $where_clause .= " AND SSPL.CategoryID = SummitEvent.CategoryID";
                    break;
                case 'alternate' :
                    $where_clause .= " AND SSP.`Order` IS NOT NULL AND SSP.`Order` > PC.SessionCount AND SSPL.ListType = 'Group'";
                    $where_clause .= " AND SSPL.CategoryID = SummitEvent.CategoryID";
                    break;
            }
        }

        if (!empty($search_term)) {
            $where_clause .= " AND (SummitEvent.Title LIKE '%{$search_term}%'
                                    OR SummitEvent.ID = '{$search_term}'
                                    OR SummitEvent.Abstract LIKE '%{$search_term}%')";
        }

        $list      = SummitEvent::get()
            ->filter( array('SummitID' => $summit_id, 'Published' => 0))
            ->leftJoin("SummitEventType","SummitEventType.ID = SummitEvent.TypeID")
            ->leftJoin('SummitSelectedPresentation','SSP.PresentationID = SummitEvent.ID','SSP')
            ->leftJoin('SummitSelectedPresentationList','SSP.SummitSelectedPresentationListID = SSPL.ID','SSPL')
            ->leftJoin('PresentationCategory','PC.ID = SummitEvent.CategoryID','PC')
            ->where($where_clause)->sort("TRIM({$order})");

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
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function getPublishedByTimeFrame($summit_id, $start_date, $end_date)
    {
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');

        $start_date = $summit->convertDateFromTimeZone2UTC($start_date);
        $end_date   = $summit->convertDateFromTimeZone2UTC($end_date);

        $list      = SummitEvent::get()
            ->filter( array('SummitID' => $summit_id, 'Published' => 1 ))
            ->where("StartDate < '{$end_date}' AND EndDate > '{$start_date}'");

        return $list->toArray();
    }

    /**
     * @param ISummit $summit
     * @param $days
     * @param $start_time
     * @param $end_time
     * @param $locations
     * @return array
     */
    public function getPublishedByTimeAndVenue(ISummit $summit, $days, $start_time, $end_time, $locations)
    {
        $where_clause = '';

        if (!empty($days)) {
            $where_clause .= "(";
            foreach($days as $day) {
                $start_date = $summit->convertDateFromTimeZone2UTC($day.' '.$start_time);
                $end_date = $summit->convertDateFromTimeZone2UTC($day.' '.$end_time);
                $where_clause .= "(StartDate < '{$end_date}' AND EndDate > '{$start_date}') OR ";
            }
            $where_clause = substr($where_clause, 0, -4).")";
        }

        if (!empty($locations)) {
            $locations_string = implode(',',$locations);
            $where_clause .= " AND LocationID IN ({$locations_string})";
        }


        $events = SummitEvent::get()
            ->filter( array('SummitID' => $summit->getIdentifier(), 'Published' => 1 ))
            ->where($where_clause)->sort('LocationID,StartDate')->toArray();

        return $events;
    }

    /**
     * Returns the blackout events for this day on every other location
     * @param ISummit $summit
     * @param mixed $day
     * @param int $location_id
     * @return array
     */
    public function getOtherBlackoutsByDay(ISummit $summit, $day, $location_id) {
        if (is_null($summit)) throw new InvalidArgumentException('summit not found!');
        if (is_null($day) || is_null($location_id)) throw new InvalidArgumentException('need a day and a location!');

        if (!$day instanceof DateTime) {
            $day = new DateTime($day);
        }

        $start = $day->setTime(0, 0, 0)->format("Y-m-d H:i:s");
        $end = $day->add(new DateInterval('PT23H59M59S'))->format("Y-m-d H:i:s");

        $start_date = $summit->convertDateFromTimeZone2UTC($start);
        $end_date = $summit->convertDateFromTimeZone2UTC($end);

        $list = SummitEvent::get()
            ->leftJoin('SummitEventType','SummitEventType.ID = SummitEvent.TypeID')
            ->where("SummitEvent.SummitID = {$summit->getIdentifier()} AND SummitEvent.Published = 1
                     AND SummitEvent.StartDate < '{$end_date}' AND SummitEvent.EndDate > '{$start_date}'
                     AND SummitEvent.LocationID != {$location_id} AND SummitEventType.BlackOutTimes = 1");

        return $list->toArray();
    }

    /**
     * @param ISummit $summit
     * @param string $term
     * @return ISummitEvent[]
     */
    public function searchBySummitTermAndHasRSVP(ISummit $summit, $term)
    {

        $events = array();

        $summit_id = $summit->getIdentifier();

        $sql_events   = <<<SQL
        SELECT * FROM (
            SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1 AND E.RSVPTemplateID > 0
                AND (Title LIKE '%{$term}%' OR E.ID = '{$term}')
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1 AND E.RSVPTemplateID > 0
                AND EXISTS
                (
                    SELECT T.ID FROM Tag T INNER JOIN SummitEvent_Tags ET ON ET.TagID = T.ID
                    WHERE ET.SummitEventID = E.ID AND T.Tag LIKE '%{$term}%'
                )
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1 AND E.RSVPTemplateID > 0
                AND EXISTS
                (
                    SELECT P.ID FROM Presentation P
                    INNER JOIN SummitEvent E2 ON E2.ID = P.ID                   
                    LEFT JOIN PresentationCategory PC ON PC.ID = E2.CategoryID
                    WHERE  P.ID = E.ID AND PC.Title LIKE '%{$term}%'
                )
            UNION SELECT DISTINCT E.* FROM SummitEvent E LEFT JOIN SummitEventType ET ON ET.ID = E.TypeID
                WHERE E.SummitID = {$summit_id} AND E.Published = 1 AND E.RSVPTemplateID > 0
                AND ET.Type LIKE '%{$term}%'
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1 AND E.RSVPTemplateID > 0
                AND EXISTS
                (
                    SELECT P.ID FROM Presentation P
                    WHERE  P.ID = E.ID AND P.Level LIKE '%{$term}%'
                )
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1 AND E.RSVPTemplateID > 0
                AND EXISTS
                (
                    SELECT P.ID, CONCAT(S.FirstName,' ',S.LastName) AS SpeakerFullName  From Presentation P
                    INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
                    INNER JOIN PresentationSpeaker S ON S.ID = PS.PresentationSpeakerID
                    WHERE P.ID = E.ID
                    HAVING
                        SpeakerFullName LIKE '%{$term}%'
                        OR SOUNDEX(SpeakerFullName) = SOUNDEX('{$term}')
                )
            UNION SELECT DISTINCT E.* FROM SummitEvent E
                WHERE E.SummitID = {$summit_id} AND E.Published = 1 AND E.RSVPTemplateID > 0
                AND EXISTS
                (
                    SELECT P.ID, CONCAT(S.FirstName,' ',S.LastName) AS SpeakerFullName  From Presentation P
                    INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
                    INNER JOIN PresentationSpeaker S ON S.ID = PS.PresentationSpeakerID
                    WHERE P.ID = E.ID AND S.ID = '{$term}'
                )
SQL;

        foreach(DB::query($sql_events.") AS Q1 ORDER BY StartDate ASC, EndDate ASC ;") as $row)
        {
            $class = $row['ClassName'];
            array_push($events, new $class($row));
        }

        return $events;
    }

    /**
     * @param int $summit_id
     * @return array
     */
    public function getCurrentPublished($summit_id)
    {
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');

        $current_date = gmdate('d/m/Y H:i:s');

        $list = SummitEvent::get()
            ->filter( array('SummitID' => $summit_id, 'Published' => 1 ))
            ->where("StartDate < '{$current_date}' AND EndDate > '{$current_date}'");

        return $list;
    }
}