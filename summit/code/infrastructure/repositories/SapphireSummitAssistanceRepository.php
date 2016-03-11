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

        $offset = ($page - 1 ) * $page_size;


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
LIMIT {$offset}, {$page_size};
SQL;

        $data = DB::query($query);
        $result = array('Total' => $total, 'Data' => $data);

        return $result;
    }

    public function getRoomsBySummitAndDay($summit_id,$date)
    {

        $query = <<<SQL
SELECT E.StartDate AS start_date, E.EndDate AS end_date, E.Title AS event, L.Name AS room, COUNT(PS.PresentationSpeakerID) AS speakers
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