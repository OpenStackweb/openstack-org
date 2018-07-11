<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class CFPTrackTagGroupsRefactoringMigration
 */
final class CFPTrackTagGroupsRefactoringMigration extends AbstractDBMigrationTask
{
    protected $title = "CFPTrackTagGroupsRefactoringMigration";

    protected $description = "CFPTrackTagGroupsRefactoringMigration";

    function doUp()
    {
       global $database;

       $queries = [

           DBSchema::existsTable($database, 'TagGroup') ?  "INSERT INTO DefaultTrackTagGroup
(ClassName, Created, LastEdited, `Name`, Label, `Order`, Mandatory)
SELECT 'DefaultTrackTagGroup', NOW(), NOW(), TRIM(`Name`), TRIM(`Label`), `Order`, Mandatory FROM 
TagGroup;" : "",
           DBSchema::existsTable($database, 'Summit_CategoryDefaultTags') ?  "INSERT INTO DefaultTrackTagGroup_AllowedTags
(DefaultTrackTagGroupID, TagID)
SELECT DefaultTrackTagGroup.ID, Tag.ID
FROM Summit_CategoryDefaultTags 
INNER JOIN DefaultTrackTagGroup ON DefaultTrackTagGroup.`Name` = Summit_CategoryDefaultTags.`Group`
INNER JOIN Tag ON Tag.ID = Summit_CategoryDefaultTags.TagID
WHERE SummitID = 24;" : "",
           "DROP TABLE IF EXISTS `Summit_CategoryDefaultTags`;",
           "DROP TABLE IF EXISTS `TagGroup`;",
           "INSERT INTO TrackTagGroup
(Created, LastEdited, `Name`, `Label`, `Order`, `Mandatory`, SummitID)
SELECT NOW(), NOW(), `Name`, `Label`, `Order`, `Mandatory`, 24 FROM DefaultTrackTagGroup;",
           "INSERT INTO TrackTagGroup_AllowedTags
(TrackTagGroupID, TagID, IsDefault)
SELECT TrackTagGroup.ID, Tag.ID, 1 
FROM DefaultTrackTagGroup_AllowedTags
INNER JOIN DefaultTrackTagGroup ON DefaultTrackTagGroup.ID = DefaultTrackTagGroup_AllowedTags.DefaultTrackTagGroupID
INNER JOIN Tag ON Tag.ID = DefaultTrackTagGroup_AllowedTags.TagID
INNER JOIN TrackTagGroup ON DefaultTrackTagGroup.`Label` = TrackTagGroup.`Label`;",
           DBSchema::existsColumn($database, 'PresentationCategory_AllowedTags', `Group`) ? "ALTER TABLE PresentationCategory_AllowedTags DROP COLUMN `Group`;" : "",
           DBSchema::existsColumn($database, 'PresentationCategory_AllowedTags', `IsDefault`) ? "ALTER TABLE PresentationCategory_AllowedTags DROP COLUMN `IsDefault`;": ""
       ];

       foreach($queries as $sql){
           if(empty($sql)) continue;
           DB::query($sql);
       }
    }

    function doDown()
    {

    }
}