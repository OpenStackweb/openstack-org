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
        $events = array();

        $summit_id = $summit->getIdentifier();

        $sql_events   = <<<SQL
        SELECT DISTINCT E.* FROM SummitEvent E
        WHERE
        E.SummitID = {$summit_id} AND E.Published = 1
        AND Title LIKE '%{$term}%'
        UNION
        SELECT DISTINCT E.* FROM SummitEvent E
        WHERE
        E.SummitID = {$summit_id} AND E.Published = 1
        AND
        EXISTS
        (
            SELECT T.ID FROM Tag T INNER JOIN SummitEvent_Tags ET ON ET.TagID = T.ID
            WHERE ET.SummitEventID = E.ID AND T.Tag LIKE '%{$term}%'
        )
        UNION
        SELECT DISTINCT E.* FROM SummitEvent E
        WHERE
        E.SummitID = {$summit_id} AND E.Published = 1
        AND
        EXISTS
        (
            SELECT P.ID FROM Presentation P
            WHERE  P.ID = E.ID AND P.Level LIKE '%{$term}%'
        )
        UNION
        SELECT DISTINCT E.* FROM SummitEvent E
        WHERE
        E.SummitID = {$summit_id} AND E.Published = 1
        AND EXISTS
        (
            SELECT P.ID, CONCAT(S.FirstName,' ',S.LastName) AS SpeakerFullName  From Presentation P
            INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
            INNER JOIN PresentationSpeaker S ON S.ID = PS.PresentationSpeakerID
            WHERE P.ID = E.ID
            HAVING SpeakerFullName LIKE '%{$term}%'
        )
        UNION
        SELECT DISTINCT E.* FROM SummitEvent E
        WHERE
        E.SummitID = {$summit_id} AND E.Published = 1
        AND EXISTS
        (
            SELECT P.ID, CONCAT(S.FirstName,' ',S.LastName) AS SpeakerFullName  From Presentation P
            INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
            INNER JOIN PresentationSpeaker S ON S.ID = PS.PresentationSpeakerID
            WHERE P.ID = E.ID
            HAVING SOUNDEX(SpeakerFullName) = SOUNDEX('{$term}')
        )
SQL;

        foreach(DB::query($sql_events," ORDER BY E.StartDate ASC, E.EndDate ASC ;") as $row)
        {
            $class = $row['ClassName'];
            array_push($events, new $class($row));
        }

        return $events;
    }

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getUnpublishedBySummit($summit_id, $event_type = null, $search_term, $page = 1, $page_size = 10, $order = null)
    {
        if(is_null($order)) $order = array('SummitEvent.Created' => 'ASC');

        $where_clause = " SummitEvent.Title IS NOT NULL AND SummitEvent.Title <>'' ";
        if (!empty($search_term)) {
            $where_clause .= "AND (SummitEvent.Title LIKE '%{$search_term}%' OR SummitEvent.Description LIKE '%{$search_term}%') ";
        }
        if(!empty($event_type)){
            $where_clause .= " AND SummitEvent.TypeID = {$event_type} ";
        }

        $list      = SummitEvent::get()
            ->filter( array('SummitID' => $summit_id, 'Published' => 0, 'ClassName:ExactMatch:not' => 'Presentation' ))
            ->where($where_clause)->sort("TRIM({$order})");

        $count     = intval($list->count());
        $offset    = ($page - 1 ) * $page_size;
        $data      = $list->limit($page_size, $offset);
        return array($page, $page_size, $count, $data);
    }

    /**
     * @param int $summit_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function getPublishedByTimeFrame($summit_id,$start_date, $end_date)
    {
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');

        $start_date = $summit->convertDateFromTimeZone2UTC($start_date);
        $end_date = $summit->convertDateFromTimeZone2UTC($end_date);

        $list      = SummitEvent::get()
            ->filter( array('SummitID' => $summit_id, 'Published' => 1 ))
            ->where("StartDate < '{$end_date}' AND EndDate > '{$start_date}'");

        return $list->toArray();
    }
}