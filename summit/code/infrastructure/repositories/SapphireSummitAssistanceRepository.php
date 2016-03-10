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

    private $cache = array();

    public function __construct()
    {
        parent::__construct(new PresentationSpeakerSummitAssistanceConfirmationRequest);
    }

    /**
     * @param int $summit_id
     * @return array
     */
    public function getAssistanceBySummit($summit_id, $page, $page_size, $sort, $sort_dir)
    {
        $total_list = PresentationSpeakerSummitAssistanceConfirmationRequest::get()->filter(array('SummitID' => $summit_id ));
        $total  = $total_list->count();

        $query = <<<SQL
SELECT SA.ID AS id, S.ID AS speaker_id, M.ID AS member_id, CONCAT(S.FirstName,' ',S.LastName) AS name,
       M.Email AS email, SA.OnSitePhoneNumber AS phone, O.Name AS company, E.Title AS presentation, C.Title AS track,
       SA.IsConfirmed AS confirmed, SA.RegisteredForSummit AS registered, SA.CheckedIn AS checked_in
FROM PresentationSpeaker AS S
INNER JOIN PresentationSpeakerSummitAssistanceConfirmationRequest AS SA ON S.ID = SA.SpeakerID AND SA.SummitID = {$summit_id}
LEFT JOIN Member AS M ON M.ID = S.MemberID
LEFT JOIN Affiliation AS A ON A.MemberID = M.ID
LEFT JOIN Org AS O ON O.ID = A.OrganizationID
LEFT JOIN Presentation_Speakers AS PS ON PS.PresentationSpeakerID = S.ID
LEFT JOIN Presentation AS P ON P.ID = PS.PresentationID
LEFT JOIN SummitEvent AS E ON E.ID = PS.PresentationID AND E.SummitID = {$summit_id}
LEFT JOIN PresentationCategory AS C ON C.ID = P.CategoryID
GROUP BY S.ID
ORDER BY {$sort} {$sort_dir}
SQL;
        if ($page && $page_size) {
            $offset = ($page - 1 ) * $page_size;
            $query .=
                <<<SQL
 LIMIT {$offset}, {$page_size};
SQL;

        }

        $data = DB::query($query);
        $result = array('Total' => $total, 'Data' => $data);

        return $result;
    }

    public function getPresentationsAndSpeakersBySummit($summit_id, $page, $page_size, $sort, $sort_dir)
    {
        $query_body =
            " FROM (
                SELECT
                    E.ID,
                    CASE WHEN SP.`Order`<= PC.SessionCount THEN 'accepted' ELSE 'alternate' END AS SelectionStatus,
                    E.Published
                FROM SummitEvent AS E
                INNER JOIN Presentation AS P ON P.ID = E.ID
                INNER JOIN PresentationCategory AS PC ON PC.ID = P.CategoryID
                LEFT JOIN SummitSelectedPresentation AS SP ON SP.PresentationID = P.ID
                LEFT JOIN SummitSelectedPresentationList AS SPL ON SP.SummitSelectedPresentationListID = SPL.ID
                WHERE E.SummitID = {$summit_id} AND SPL.ListType = 'Group'
                UNION
                SELECT E.ID, 'unaccepted' AS SelectionStatus, E.Published
                FROM SummitEvent AS E
                INNER JOIN Presentation AS P ON P.ID = E.ID
                WHERE E.SummitID = {$summit_id} AND NOT EXISTS (
                    SELECT 1 FROM SummitSelectedPresentation AS SP
                    INNER JOIN SummitSelectedPresentationList AS SPL ON SP.SummitSelectedPresentationListID = SPL.ID
                    WHERE SP.PresentationID = P.ID AND SPL.ListType = 'Group'
                )
            ) AS Q1
            INNER JOIN Presentation_Speakers AS PS ON PS.PresentationID = Q1.ID
            INNER JOIN PresentationSpeaker AS S ON S.ID = PS.PresentationSpeakerID
            LEFT JOIN SpeakerRegistrationRequest AS SRR ON SRR.SpeakerID = S.ID
            LEFT JOIN Member AS M ON M.ID = S.MemberID
            LEFT JOIN SpeakerSummitRegistrationPromoCode AS SRPC ON SRPC.SpeakerID = S.ID
            LEFT JOIN SummitRegistrationPromoCode AS RPC ON RPC.ID = SRPC.ID
            LEFT JOIN SpeakerAnnouncementSummitEmail AS ASE ON ASE.SpeakerID = S.ID
            LEFT JOIN SummitEvent AS E ON E.ID = Q1.ID
            LEFT JOIN Presentation AS P ON P.ID = Q1.ID
            LEFT JOIN SummitAbstractLocation AS L ON L.ID = E.LocationID
            LEFT JOIN PresentationCategory AS PC ON P.CategoryID = PC.ID
            LEFT JOIN PresentationSpeakerSummitAssistanceConfirmationRequest AS ACR ON ACR.SpeakerID = S.ID
            WHERE RPC.SummitID = {$summit_id} AND ACR.SummitID={$summit_id} AND L.`Name` IS NOT NULL";

        $query_count = "SELECT COUNT(*)";

        $query =
            "SELECT
                Q1.ID AS presentation_id,
                ACR.id AS assistance_id,
                E.Title AS presentation,
                Q1.Published AS published,
                Q1.SelectionStatus AS status,
                PC.Title AS track,
                E.StartDate AS start_date,
                L.Name AS location,
                S.ID AS speaker_id,
                S.MemberID AS member_id,
                CONCAT(S.FirstName,' ',S.LastName) AS name,
                IFNULL(M.Email, SRR.Email) AS email,
                ACR.OnSitePhoneNumber AS phone,
                SRPC.Type AS code_type,
                RPC.Code AS promo_code,
                ACR.IsConfirmed AS confirmed,
                ACR.RegisteredForSummit AS registered,
                ACR.CheckedIn AS checked_in";

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

    public function getRoomsBySummitAndDay($summit_id,$date)
    {

        $query = <<<SQL
SELECT E.StartDate AS start_date, E.EndDate AS end_date, 'K' AS code, E.Title AS event, L.Name AS room, COUNT(PS.PresentationSpeakerID) AS speakers
FROM Presentation AS P
LEFT JOIN Presentation_Speakers AS PS ON PS.PresentationID = P.ID
INNER JOIN PresentationSpeakerSummitAssistanceConfirmationRequest AS SA ON PS.PresentationSpeakerID = SA.SpeakerID AND SA.SummitID = {$summit_id}
LEFT JOIN SummitEvent AS E ON E.ID = P.ID AND E.SummitID = {$summit_id}
LEFT JOIN SummitAbstractLocation AS L ON L.ID = E.LocationID
WHERE DATE(E.StartDate) = '{$date}'
GROUP BY P.ID
SQL;

        return DB::query($query);
    }
}