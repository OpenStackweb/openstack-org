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
 * Class IndividualFoundationMemberCountQuery
 */
class SpeakersExportQuery implements IQueryHandler {
    /**
     * @param IQuerySpecification $specification
     * @return IQueryResult
     */
    public function handle(IQuerySpecification $specification) {
        $params = $specification->getSpecificationParams();
        $selectedSummits = $params["selectedSummits"];
        $onlyApprovedSpeakers = $params["onlyApprovedSpeakers"];
        $affiliation = $params["affiliation"];

        if (count($selectedSummits)) {
            $inCondition = "(";
            $inConditionSeparator = "";
            foreach($selectedSummits as $summitId) {
                $inCondition .= "{$inConditionSeparator}{$summitId}";
                $inConditionSeparator = ",";
            }
            $inCondition .= ")";
        }

        $whereAdded = false;
        $whereClause = "";
        if (count($selectedSummits)) {
            if (!$whereAdded) {
                $whereClause = "WHERE ";
                $whereAdded = true;
            }
            $whereClause.= "E.SummitID IN {$inCondition}";
        }

        if ($onlyApprovedSpeakers) {
            if (!$whereAdded) {
                $whereClause = "WHERE ";
                $whereAdded = true;
            }
            else {
                $whereClause .= " AND ";
            }
            $whereClause.= "(PR.Progress BETWEEN 0 AND 2) AND (Status IS NULL)";
        }

        if (isset($affiliation) && strlen($affiliation) > 0) {
            if (!$whereAdded) {
                $whereClause = "WHERE ";
                $whereAdded = true;
            }
            else {
                $whereClause .= " AND ";
            }
            $whereClause.= "OG.Name like '%{$affiliation}%'";
        }

        $query = "SELECT DISTINCT
Surname,
FirstName,
Email,
E.Title as PresentationTitle,
Progress as PresentationProgress,
Status as PresentationStatus
FROM Presentation PR
LEFT JOIN SummitEvent AS E ON PR.ID = E.ID
INNER JOIN Presentation_Speakers PS ON PR.Id = PS.PresentationID
INNER JOIN Member ME ON ME.Id = PS.PresentationSpeakerID
LEFT JOIN Affiliation AF ON AF.MemberID = ME.ID
INNER JOIN Org OG ON OG.ID = AF.OrganizationID
{$whereClause}
ORDER BY Surname, FirstName";

        $result = DB::query($query);

        return new AbstractQueryResult(array($result));
    }
}