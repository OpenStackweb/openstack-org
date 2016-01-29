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
class RemovePresentationDeprecatedColumnsMigration extends AbstractDBMigrationTask
{
    protected $title = "RemovePresentationDeprecatedColumnsMigration";

    protected $description = "RemovePresentationDeprecatedColumnsMigration";

    function doUp()
    {
        global $database;

        $columns = array
        (
            'LastEdited',
            'Created',
            'Name',
            'DisplayOnSite',
            'Featured',
            'City',
            'Country',
            'YouTubeID',
            'URLSegment',
            'StartTime',
            'EndTime',
            'Type',
            'Location',
            'Day',
            'Speakers',
            'SlidesLink',
            'event_key',
            'MemberID',
            'IsKeynote',
        );

        foreach($columns as $column) {
            if (DBSchema::existsColumn($database, 'Presentation', $column)) {
                DB::query("ALTER TABLE Presentation DROP COLUMN {$column};");
            }
        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}