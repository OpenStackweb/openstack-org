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
 * Class SpeakersSubmissionsExportQuery
 */
class SpeakersSubmissionsExportQuery implements IQueryHandler {
    /**
     * @param IQuerySpecification $specification
     * @return IQueryResult
     */
    public function handle(IQuerySpecification $specification) {
        $params = $specification->getSpecificationParams();
        $selectedSummits = implode(',',$params["selectedSummits"]);
        $statusPrimary = $params["statusPrimary"];
        $statusAlternate = $params["statusAlternate"];

        $statuses = array();

        if ($statusPrimary) $statuses[] = 'PRIMARY';
        if ($statusAlternate) $statuses[] = 'ALTERNATE';

        $statuses = "'".implode("','",$statuses)."'";

        $query = "SELECT *,
            IF(`Order` <= SessionCount, 'PRIMARY', 'ALTERNATE') AS AcceptedStatus
            FROM (
                SELECT S.FirstName, S.LastName, S.MemberID, E.Title AS PresentationTitle,
                P.ID as PresentationID, PC.Title AS Track, SSP.`Order`, PC.SessionCount, PC.AlternateCount
                FROM PresentationSpeaker S
                INNER JOIN Presentation_Speakers PS on PS.PresentationSpeakerID = S.ID
                INNER JOIN Presentation P ON P.ID = PS.PresentationID
                INNER JOIN SummitEvent E ON E.ID = P.ID
                INNER JOIN PresentationCategory PC ON PC.ID = P.CategoryID
                LEFT  JOIN SummitSelectedPresentation SSP ON SSP.PresentationID = P.ID
                LEFT  JOIN SummitSelectedPresentationList SSPL ON SSPL.ID = SSP.SummitSelectedPresentationListID
                WHERE E.SummitID IN ( $selectedSummits ) AND SSPL.ListType = 'Group'
            ) AS REPORT_2 HAVING AcceptedStatus IN ( $statuses );";

        $result = DB::query($query);

        return new AbstractQueryResult(array($result));
    }
}