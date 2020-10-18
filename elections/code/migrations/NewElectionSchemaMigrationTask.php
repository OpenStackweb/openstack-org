<?php
/**
 * Copyright 2018 Open Infrastructure Foundation
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

final class NewElectionSchemaMigrationTask extends AbstractDBMigrationTask {

    protected $title = "NewElectionSchemaMigrationTask";

    protected $description = "NewElectionSchemaMigrationTask";

    function doUp()
    {
        global $database;

        ElectionPage::$validation_enabled = false;

        $queries = [
            "UPDATE SiteTree SET ClassName = 'ElectionsHolderPage'
where ClassName ='ElectionSystem';",
            "UPDATE SiteTree_Live SET ClassName = 'ElectionsHolderPage'
where ClassName ='ElectionSystem';",
            "UPDATE SiteTree_versions SET ClassName = 'ElectionsHolderPage'
where ClassName ='ElectionSystem';",
            "INSERT INTO `Election`
(
`ElectionsOpen`,
`ElectionsClose`,
`Name`,
`NominationsOpen`,
`NominationsClose`,
`NominationAppDeadline`,
`TimeZoneIdentifier`
)
VALUES
(
'2018-01-08',
'2018-01-12',
 NULL,
'2017-11-27',
'2017-12-12',
'2017-12-12',
NULL
);",
            "UPDATE Election E
LEFT JOIN ElectionPage EP ON E.ElectionsOpen = EP.ElectionsOpen
SET E.NominationsOpen = EP.NominationsOpen,
	 E.NominationsClose = EP.NominationsClose,
     E.NominationAppDeadline = EP.NominationAppDeadline;",
            "UPDATE ElectionPage EP
LEFT JOIN Election E ON E.ElectionsOpen = EP.ElectionsOpen
SET EP.CurrentElectionID = E.ID;",
            "ALTER TABLE ElectionPage DROP COLUMN NominationsOpen;",
            "ALTER TABLE ElectionPage DROP COLUMN NominationsClose;",
            "ALTER TABLE ElectionPage DROP COLUMN NominationAppDeadline;",
            "ALTER TABLE ElectionPage DROP COLUMN ElectionsOpen;",
            "ALTER TABLE ElectionPage DROP COLUMN ElectionsClose;",
            "ALTER TABLE ElectionPage DROP COLUMN ElectionActive;",
            "UPDATE Election
Set Name =  CONCAT(MONTHNAME(ElectionsOpen),' ', YEAR(ElectionsOpen),' Board Election');",
            "UPDATE Candidate C
LEFT JOIN ElectionPage EP ON EP.ID = C.ElectionID
SET C.ElectionID = EP.CurrentElectionID;",
            "UPDATE CandidateNomination CN
LEFT JOIN ElectionPage EP ON EP.ID = CN.ElectionID
SET CN.ElectionID = EP.CurrentElectionID;",
            "INSERT INTO ElectionVote
(ID, ClassName, Created, LastEdited, VoterID, ElectionID)
SELECT ID,'ElectionVote' , Created, LastEdited, VoterID, ElectionID
FROM Vote;",
            "DROP TABLE Vote;",
            "ALTER TABLE Candidate DROP COLUMN Nominated;",
            "UPDATE Election
SET 
ElectionsOpen =  CONCAT(DATE(ElectionsOpen), ' 08:00:00'),
ElectionsClose =  CONCAT(DATE(ElectionsClose), ' 07:59:00'),
NominationsOpen =  CONCAT(DATE(NominationsOpen), ' 08:00:00'),
NominationsClose =  CONCAT(DATE(NominationsClose), ' 07:59:00'),
NominationAppDeadline =  CONCAT(DATE(NominationAppDeadline), ' 07:59:00'),
TimeZoneIdentifier = 'America/Los_Angeles';"
        ];

        foreach($queries as $sql)
            DB::query($sql);

    }

}