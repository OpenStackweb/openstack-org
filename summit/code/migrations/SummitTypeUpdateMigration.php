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


final class SummitTypeUpdateMigration extends AbstractDBMigrationTask
{
    protected $title = "SummitTypeUpdateMigration";

    protected $description = "SummitTypeUpdateMigration";

    function doUp()
    {
        global $database;

        DB::query("DELETE FROM SummitType;");

        if(DBSchema::existsColumn($database, "SummitType", "SummitID")){
            DBSchema::dropColumn($database, "SummitType", "SummitID");
        }

        if(DBSchema::existsColumn($database, "SummitType", "StartDate")){
            DBSchema::dropColumn($database, "SummitType", "StartDate");
        }

        if(DBSchema::existsColumn($database, "SummitType", "EndDate")){
            DBSchema::dropColumn($database, "SummitType", "EndDate");
        }

        if(DBSchema::existsColumn($database, "SummitType", "Title")){
            DBSchema::dropColumn($database, "SummitType", "Title");
        }

        DBSchema::dropTable($database, 'SummitEvent_AllowedSummitTypes');

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}