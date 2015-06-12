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

final class FixVideoPageTask extends MigrationTask {

    protected $title = "Fix Video Page";

    protected $description = "Fix Video Page";

    function up()
    {
        echo "Starting Migration Proc ...<BR>";
        //check if migration already had ran ...
        $migration = Migration::get()->filter('Name', $this->title)->first();

        if (!$migration) {

$SQL = <<<SQL
INSERT INTO `VideoPresentation`
(
`ClassName`,
`Created`,
`LastEdited`,
`Name`,
`DisplayOnSite`,
`Featured`,
`City`,
`Country`,
`Description`,
`YouTubeID`,
`URLSegment`,
`StartTime`,
`EndTime`,
`Location`,
`Type`,
`Day`,
`Speakers`,
`SlidesLink`,
`event_key`,
`IsKeynote`,
`PresentationCategoryPageID`,
`SummitID`,
`MemberID`)
SELECT 'VideoPresentation', NOW(),NOW(),Name, `DisplayOnSite`,
`Featured`,
`City`,
`Country`,
`Description`,
`YouTubeID`,
`URLSegment`,
`StartTime`,
`EndTime`,
`Location`,
`Type`,
`Day`,
`Speakers`,
`SlidesLink`,
`event_key`,
`IsKeynote`,
`PresentationCategoryPageID`,
`SummitID`,
`MemberID` FROM Presentation;
SQL;


            DB::query($SQL);

            $migration = new Migration();
            $migration->Name = $this->title;
            $migration->Description = $this->description;
            $migration->Write();
        }
        echo "Ending  Migration Proc ...<BR>";
    }

    function down()
    {

    }

}