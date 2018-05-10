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
final class SapphireSummitAssistanceRepository extends SapphireRepository implements ISummitAssistanceRepository
{

    public function __construct()
    {
        parent::__construct(new PresentationSpeakerSummitAssistanceConfirmationRequest);
    }

    /**
     * @param int $summit_id
     * @return array
     */
    public function getAssistanceBySummit($summit_id, $page, $page_size, $sort, $sort_dir, $filter, $search_term)
    {

        $select = <<<SQL
SELECT 
Q1.EventID as presentation_id,
Q1.EventTitle as presentation, 
Q1.SpeakerID as speaker_id,
M.ID AS member_id,
IFNULL(CONCAT(M.FirstName ,' ',M.Surname), CONCAT(SP.FirstName ,' ', SP.LastName)) AS name,
IFNULL(M.Email, SPRR.Email) AS email,
ACR.OnSitePhoneNumber AS phone,
IFNULL(ACR.IsConfirmed, 0) AS confirmed,
IFNULL(ACR.RegisteredForSummit, 0) AS registered,
IFNULL(ACR.CheckedIn, 0) AS checked_in,
O.Name AS company,
PresentationCategory.Title AS track
SQL;

        $from = <<<SQL
         FROM ( 
SELECT E.ID AS EventID, E.Title AS EventTitle, IFNULL(SP.ID, SP2.ID) as SpeakerID, E.CategoryID 
FROM SummitEvent AS E
INNER JOIN Presentation AS P ON P.ID = E.ID
LEFT JOIN Presentation_Speakers AS PS ON PS.PresentationID = P.ID
LEFT JOIN PresentationSpeaker SP ON SP.ID = PS.PresentationSpeakerID
LEFT JOIN PresentationSpeaker SP2 ON SP2.ID = P.ModeratorID
WHERE 
E.SummitID = {$summit_id}
AND E.Published = 1 
) 
AS Q1
INNER JOIN PresentationSpeaker SP ON SP.ID = Q1.SpeakerID
INNER JOIN Member M ON SP.MemberID = M.ID
INNER JOIN PresentationCategory  ON PresentationCategory.ID = Q1.CategoryID
LEFT JOIN SpeakerRegistrationRequest SPRR ON SPRR.SpeakerID = SP.ID
LEFT JOIN PresentationSpeakerSummitAssistanceConfirmationRequest ACR ON ACR.SpeakerID = SP.ID AND ACR.SummitID = {$summit_id}
LEFT JOIN Affiliation AS A ON A.ID = (
                SELECT ID FROM Affiliation
                WHERE
                Affiliation.MemberID = M.ID
                AND Affiliation.Current = 1
                ORDER BY EndDate DESC
                LIMIT 1
)
LEFT JOIN Org AS O ON O.ID = A.OrganizationID
SQL;

$where = '';
        if (!empty($search_term)) {
            $search_term = Convert::raw2sql($search_term);
            $where = "(SPRR.Email LIKE '%$search_term%' OR M.Email LIKE '%$search_term%'
                       OR M.Surname LIKE '%$search_term%' OR SP.LastName LIKE '%$search_term%' OR O.Name LIKE '%$search_term%') ";
        }

        if ($filter != 'all') {

            if(!empty($where))
                $where .= 'AND ';

            if ($filter == 'hide_confirmed')
                $where .= " ACR.IsConfirmed = 0 ";
            else if ($filter == 'hide_registered')
                $where .= " ACR.RegisteredForSummit = 0 ";
            else if ($filter == 'hide_checkedin')
                $where .= " ACR.CheckedIn = 0 ";
            else if ($filter == 'hide_all')
                $where .= "  ( ACR.IsConfirmed = 0 AND ACR.RegisteredForSummit = 0 AND ACR.CheckedIn = 0 ) ";
        }

        if(!empty($where))
            $where = ' WHERE '.$where;

        $query = <<<SQL
            {$select}
            {$from}
            {$where}
            ORDER BY {$sort} {$sort_dir}
SQL;


        $query_count = <<<SQL
            SELECT COUNT(*) FROM (
                {$select}
                {$from}
                {$where}
            ) as q1
SQL;

        if ($page && $page_size) {
            $offset = ($page - 1 ) * $page_size;
            $query .=
                <<<SQL
 LIMIT {$offset}, {$page_size};
SQL;

        }
        $total  = DB::query($query_count)->value();
        $data   = DB::query($query);
        $result = array('Total' => $total, 'Data' => $data);

        return $result;
    }

    public function getRoomsBySummitAndDay($summit, $date, $event_type='all', $venues='', $tracks='' ,$sort_by)
    {
        $start_date = $summit->convertDateFromTimeZone2UTC($date.' 00:00:00');
        $end_date = $summit->convertDateFromTimeZone2UTC($date.' 23:59:59');

        $query = <<<SQL
SELECT E.ID AS id ,
0 AS date,
0 AS time,
E.StartDate AS start_date,
E.EndDate AS end_date,
PC.Code AS code,
E.Title AS event,
L.Name AS room,
L2.Name AS venue,
R.Capacity AS capacity,
(COUNT(DISTINCT(SA.SpeakerID), SA.SpeakerID IS NOT NULL) + IF(P.ModeratorID != 0, 1, 0)) AS speakers,
E.HeadCount AS headcount,
COUNT(DISTINCT A.ID) AS total,
GROUP_CONCAT(DISTINCT CONCAT(S.FirstName,' ',S.LastName) SEPARATOR ', ') AS speaker_list
FROM SummitEvent AS E
LEFT JOIN Presentation AS P ON P.ID = E.ID
LEFT JOIN PresentationCategory AS PC ON E.CategoryID = PC.ID
LEFT JOIN Presentation_Speakers AS PS ON PS.PresentationID = P.ID
LEFT JOIN PresentationSpeakerSummitAssistanceConfirmationRequest AS SA ON PS.PresentationSpeakerID = SA.SpeakerID AND SA.SummitID = {$summit->ID}
INNER JOIN SummitAbstractLocation AS L ON L.ID = E.LocationID
LEFT JOIN SummitVenueRoom AS R ON R.ID = L.ID
LEFT JOIN SummitAbstractLocation AS L2 ON L2.ID = R.VenueID
LEFT JOIN Member_Schedule AS A ON A.SummitEventID = E.ID
LEFT JOIN PresentationSpeaker AS S ON S.ID = PS.PresentationSpeakerID
WHERE E.Published = 1 AND E.StartDate > '{$start_date}' AND E.StartDate < '{$end_date}' AND E.SummitID = {$summit->ID}
SQL;
        if ($event_type == 'presentation') {
            $query .= <<<SQL
 AND E.ClassName = 'Presentation'
SQL;
        }

        if ($venues) {
            $query .= <<<SQL
 AND E.LocationID IN ( {$venues} )
SQL;
        }

        if ($tracks) {
            $query .= <<<SQL
 AND E.CategoryID IN ( {$tracks} )
SQL;
        }

        if ($sort_by != 'start_date') {
            $sort_by = $sort_by . ', start_date';
        }

        $query .= <<<SQL
 GROUP BY E.ID ORDER BY {$sort_by}
SQL;

        return DB::query($query);
    }

    public function getAttendeesWithCalendar($summit_id)
    {
        $query = <<<SQL
SELECT COUNT(DISTINCT(ASched.MemberID)) AS calcount
FROM Member_Schedule AS ASched
LEFT JOIN SummitEvent AS E ON E.Id = ASched.SummitEventID
WHERE E.SummitID = {$summit_id}
SQL;

        return DB::query($query);
    }

}