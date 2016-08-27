<?php

/**
 * Copyright 2016 OpenStack Foundation
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
final class UpdateSummitLocationImagesMigration extends AbstractDBMigrationTask
{
    protected $title = "UpdateSummitLocationImagesMigration";

    protected $description = "UpdateSummitLocationImagesMigration";

    function doUp()
    {
        global $database;

        if (DBSchema::existsTable($database, '_obsolete_SummitLocationMap')) {

            $res = DB::query("SELECT * FROM _obsolete_SummitLocationMap;");

            foreach($res as $row)
            {
                $new_location              = SummitLocationMap::create();
                $new_location->Name        = trim($row['Name']);
                $new_location->Description = trim($row['Description']);
                $new_location->PictureID   = intval($row['MapID']);
                $new_location->LocationID  = intval($row['LocationID']);
                $new_location->Created     = trim($row['Created']);
                $new_location->LastEdited  = trim($row['LastEdited']);
                $new_location->Order       = intval($row['Order']);
                $new_location->write();
            }
            DB::query("DROP TABLE _obsolete_SummitLocationMap;");
        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}