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
            S.ID AS speaker_id,
            Member.ID AS member_id,
            IFNULL(CONCAT(Member.FirstName ,' ',Member.Surname), CONCAT(S.FirstName ,' ',S.LastName)) AS name,
            IFNULL(Member.Email, SpeakerRegistrationRequest.Email) AS email,
            O.Name AS company,
            E.Title AS presentation,
            PresentationCategory.Title AS track,
            ACR.OnSitePhoneNumber AS phone,
            ACR.IsConfirmed AS confirmed,
            ACR.RegisteredForSummit AS registered,
            ACR.CheckedIn AS checked_in
SQL;

        $from = <<<SQL
            FROM SummitEvent AS E
            INNER JOIN Presentation ON Presentation.ID = E.ID
            %s
            INNER JOIN PresentationCategory  ON PresentationCategory.ID = E.CategoryID
            LEFT JOIN Member ON Member.ID = S.MemberID
            LEFT JOIN SpeakerRegistrationRequest ON SpeakerRegistrationRequest.SpeakerID = S.ID
            LEFT JOIN PresentationSpeakerSummitAssistanceConfirmationRequest AS ACR ON ACR.SpeakerID = S.ID AND ACR.SummitID = {$summit_id}
            LEFT JOIN Affiliation AS A ON A.ID = (
                SELECT ID FROM Affiliation
                WHERE
                Affiliation.MemberID = Member.ID
                AND Affiliation.Current = 1
                ORDER BY EndDate DESC
                LIMIT 1
            )
            LEFT JOIN Org AS O ON O.ID = A.OrganizationID
            WHERE
            E.SummitID = {$summit_id}
            AND E.Published = 1
SQL;

        if ($search_term) {
            $from .= " AND (SpeakerRegistrationRequest.Email = '$search_term' OR Member.Email = '$search_term'
                       OR Member.Surname = '$search_term' OR S.LastName = '$search_term' OR O.Name = '$search_term')";
        }

        if ($filter != 'all') {
            if ($filter == 'hide_confirmed')
                $from .= " AND ACR.IsConfirmed = 0";
            else if ($filter == 'hide_registered')
                $from .= " AND ACR.RegisteredForSummit = 0";
            else if ($filter == 'hide_checkedin')
                $from .= " AND ACR.CheckedIn = 0";
            else if ($filter == 'hide_all')
                $from .= " AND ACR.IsConfirmed = 0 AND ACR.RegisteredForSummit = 0 AND ACR.CheckedIn = 0";
        }

        $from_1 = sprintf($from,'INNER JOIN Presentation_Speakers ON Presentation_Speakers.PresentationID = Presentation.ID
            INNER JOIN PresentationSpeaker AS S ON S.ID = Presentation_Speakers.PresentationSpeakerID');

        $from_2 = sprintf($from,'INNER JOIN PresentationSpeaker AS S ON S.ID = Presentation.ModeratorID');


        $query = <<<SQL
            {$select}
            {$from_1}
            UNION
            {$select}
            {$from_2}
            ORDER BY {$sort} {$sort_dir}
SQL;


        $query_count = <<<SQL
            SELECT COUNT(*) FROM (
                {$select}
                {$from_1}
                UNION
                {$select}
                {$from_2}
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

    public function getRoomsBySummitAndDay($summit_id, $date, $event_type='all', $venues='')
    {

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
LEFT JOIN PresentationSpeakerSummitAssistanceConfirmationRequest AS SA ON PS.PresentationSpeakerID = SA.SpeakerID AND SA.SummitID = {$summit_id}
INNER JOIN SummitAbstractLocation AS L ON L.ID = E.LocationID
LEFT JOIN SummitVenueRoom AS R ON R.ID = L.ID
LEFT JOIN SummitAbstractLocation AS L2 ON L2.ID = R.VenueID
LEFT JOIN Member_Schedule AS A ON A.SummitEventID = E.ID
LEFT JOIN PresentationSpeaker AS S ON S.ID = PS.PresentationSpeakerID
WHERE E.Published = 1 AND DATE(E.StartDate) = '{$date}' AND E.SummitID = {$summit_id}
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

        $query .= <<<SQL
 GROUP BY E.ID ORDER BY E.StartDate
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